<?php

namespace App\Services\Sequence;

use App\Models\NumberingSequence;
use App\Support\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class NumberingSequenceService
{
    /**
     * Atomically allocate and return the next formatted business identifier.
     * Uses DB row-level locking (lockForUpdate) inside a transaction.
     *
     * @param string $documentType e.g. 'SHIPMENT', 'QUOTATION', 'BOOKING', 'HBL', 'MBL', 'INVOICE'
     * @param int|null $branchId Optional branch scope; if null, checked from TenantContext or falls back to company-wide (0)
     * @param int|null $companyId Optional company ID; if null, resolved from TenantContext
     * @param string|null $periodKey Optional period string, e.g. '2026'; if null, defaults to current year
     * @return string Formatted business number (e.g. 'SH26-000001')
     * @throws RuntimeException If no matching numbering sequence definition is configured
     */
    public function generate(
        string $documentType,
        ?int $branchId = null,
        ?int $companyId = null,
        ?string $periodKey = null
    ): string {
        $documentType = strtoupper($documentType);
        $companyId = $companyId ?? TenantContext::getCompanyId();

        if (!$companyId) {
            throw new RuntimeException("Cannot generate document number for '{$documentType}': No company context resolved.");
        }

        $branchId = $branchId ?? TenantContext::getBranchId();
        $periodKey = $periodKey ?? date('Y');

        return DB::transaction(function () use ($companyId, $branchId, $documentType, $periodKey) {
            $sequence = $this->findSequenceForUpdate($companyId, $branchId, $documentType, $periodKey);

            if (!$sequence) {
                // Try fallback to 'ALL' period key
                $sequence = $this->findSequenceForUpdate($companyId, $branchId, $documentType, 'ALL');
            }

            if (!$sequence) {
                throw new RuntimeException(
                    "No active numbering sequence configured for document '{$documentType}' " .
                    "(Company ID: {$companyId}, Branch ID: " . ($branchId ?? 'None') . ", Period: {$periodKey})."
                );
            }

            $allocatedNumber = (int) $sequence->next_number;
            $sequence->next_number = $allocatedNumber + 1;
            $sequence->save();

            return $this->formatNumber($sequence->prefix, $allocatedNumber, (int) $sequence->padding);
        });
    }

    /**
     * Preview the next document number without incrementing or locking the sequence.
     * Useful for displaying on new form views before save.
     */
    public function peekNext(
        string $documentType,
        ?int $branchId = null,
        ?int $companyId = null,
        ?string $periodKey = null
    ): ?string {
        $documentType = strtoupper($documentType);
        $companyId = $companyId ?? TenantContext::getCompanyId();

        if (!$companyId) {
            return null;
        }

        $branchId = $branchId ?? TenantContext::getBranchId();
        $periodKey = $periodKey ?? date('Y');

        $sequence = $this->findSequence($companyId, $branchId, $documentType, $periodKey)
            ?? $this->findSequence($companyId, $branchId, $documentType, 'ALL');

        if (!$sequence) {
            return null;
        }

        return $this->formatNumber($sequence->prefix, (int) $sequence->next_number, (int) $sequence->padding);
    }

    /**
     * Find sequence with row-level lock.
     */
    protected function findSequenceForUpdate(int $companyId, ?int $branchId, string $documentType, string $periodKey): ?NumberingSequence
    {
        // 1. If branchId provided, look for branch-specific sequence first
        if ($branchId) {
            $branchSeq = NumberingSequence::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('branch_scope_id', $branchId)
                ->where('document_type', $documentType)
                ->where('period_key', $periodKey)
                ->lockForUpdate()
                ->first();

            if ($branchSeq) {
                return $branchSeq;
            }
        }

        // 2. Look for company-wide sequence (branch_scope_id = 0)
        return NumberingSequence::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('branch_scope_id', 0)
            ->where('document_type', $documentType)
            ->where('period_key', $periodKey)
            ->lockForUpdate()
            ->first();
    }

    /**
     * Find sequence without locking (for preview).
     */
    protected function findSequence(int $companyId, ?int $branchId, string $documentType, string $periodKey): ?NumberingSequence
    {
        if ($branchId) {
            $branchSeq = NumberingSequence::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('branch_scope_id', $branchId)
                ->where('document_type', $documentType)
                ->where('period_key', $periodKey)
                ->first();

            if ($branchSeq) {
                return $branchSeq;
            }
        }

        return NumberingSequence::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('branch_scope_id', 0)
            ->where('document_type', $documentType)
            ->where('period_key', $periodKey)
            ->first();
    }

    /**
     * Format number with prefix and padding.
     */
    protected function formatNumber(string $prefix, int $number, int $padding): string
    {
        return $prefix . str_pad((string) $number, $padding, '0', STR_PAD_LEFT);
    }
}
