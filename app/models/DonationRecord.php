<?php
/**
 * BloodLife — DonationRecord Model
 * Verified logs and donation history tracking.
 */

require_once __DIR__ . '/../config/database.php';

class DonationRecord {

    /**
     * Log a new verified donation record and increment donor total count inside a transaction.
     *
     * @param array $data Contains donor_id, request_id, donation_date, facility_name, city, units_donated, notes
     * @return int Donation Record ID
     * @throws Exception
     */
    public static function create(array $data): int {
        $pdo = Database::getInstance();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO donation_records 
                (donor_id, request_id, donation_date, facility_name, city, units_donated, verification_status, notes)
                VALUES 
                (:donor_id, :request_id, :donation_date, :facility_name, :city, :units_donated, 'verified', :notes)
            ");

            $stmt->execute([
                'donor_id'      => (int)$data['donor_id'],
                'request_id'    => !empty($data['request_id']) ? (int)$data['request_id'] : null,
                'donation_date' => $data['donation_date'],
                'facility_name' => trim($data['facility_name']),
                'city'          => trim($data['city']),
                'units_donated' => (int)($data['units_donated'] ?? 1),
                'notes'         => trim($data['notes'] ?? '')
            ]);

            $recordId = (int)$pdo->lastInsertId();

            // Update user_profiles last_donated_at and total_donations_count
            $stmtUpdate = $pdo->prepare("
                UPDATE user_profiles
                SET last_donated_at = :donation_date,
                    total_donations_count = total_donations_count + :units
                WHERE user_id = :donor_id
            ");
            $stmtUpdate->execute([
                'donation_date' => $data['donation_date'],
                'units'         => (int)($data['units_donated'] ?? 1),
                'donor_id'      => (int)$data['donor_id']
            ]);

            $pdo->commit();
            return $recordId;

        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Donation Record Insert Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get donation records for a specific donor.
     *
     * @param int $donorId
     * @return array
     */
    public static function getByDonorId(int $donorId): array {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            SELECT d.*, r.patient_name
            FROM donation_records d
            LEFT JOIN blood_requests r ON d.request_id = r.id
            WHERE d.donor_id = :donor_id
            ORDER BY d.donation_date DESC, d.id DESC
        ");
        $stmt->execute(['donor_id' => $donorId]);
        return $stmt->fetchAll();
    }
}
