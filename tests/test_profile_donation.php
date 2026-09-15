<?php
/**
 * BloodLife — Phase 10 Profile & Donation History Test Suite
 */

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/models/UserProfile.php';
require_once __DIR__ . '/../app/models/DonationRecord.php';

echo "========================================================\n";
echo "BloodLife — Phase 10 Profile & Donation History Test Suite\n";
echo "========================================================\n";

$passed = 0;
$failed = 0;

function assertTest(bool $condition, string $description): void {
    global $passed, $failed;
    if ($condition) {
        echo "[PASS] {$description}\n";
        $passed++;
    } else {
        echo "[FAIL] {$description}\n";
        $failed++;
    }
}

try {
    // 1. Get test donor profile
    $donorProfile = UserProfile::findByEmail('donor1@bloodlife.org');
    assertTest(!empty($donorProfile), "Donor profile retrieved by email");

    $userId = $donorProfile['user_id'];

    // 2. Test profile update
    $newFullName = "Test Donor Updated Name";
    $updated = UserProfile::update($userId, [
        'full_name'          => $newFullName,
        'phone_number'       => $donorProfile['phone_number'],
        'gender'             => 'male',
        'blood_group_id'     => $donorProfile['blood_group_id'],
        'city'               => $donorProfile['city'],
        'address'            => 'Updated Street 123',
        'bio'                => 'Ready to donate anytime.',
        'is_available_donor' => 1
    ]);
    assertTest($updated, "UserProfile::update executed successfully");

    $reloaded = UserProfile::findByUserId($userId);
    assertTest($reloaded['full_name'] === $newFullName, "Verified updated full_name in database");
    assertTest($reloaded['address'] === 'Updated Street 123', "Verified updated address in database");

    // Revert full name back
    UserProfile::update($userId, [
        'full_name'          => 'Ahmed Khan (Donor)',
        'phone_number'       => $donorProfile['phone_number'],
        'gender'             => 'male',
        'blood_group_id'     => $donorProfile['blood_group_id'],
        'city'               => $donorProfile['city'],
        'address'            => $donorProfile['address'],
        'bio'                => $donorProfile['bio'],
        'is_available_donor' => 1
    ]);

    // 3. Test avatar update
    $avatarUpdated = UserProfile::updateAvatar($userId, 'avatar_test_sample.png');
    assertTest($avatarUpdated, "UserProfile::updateAvatar executed successfully");

    $reloadedAvatar = UserProfile::findByUserId($userId);
    assertTest($reloadedAvatar['avatar'] === 'avatar_test_sample.png', "Verified avatar filename in database");

    // 4. Test donation record logging
    $initialTotal = (int)($reloadedAvatar['total_donations_count'] ?? 0);
    $donationId = DonationRecord::create([
        'donor_id'      => $userId,
        'request_id'    => null,
        'donation_date' => date('Y-m-d'),
        'facility_name' => 'Unit Test Blood Bank',
        'city'          => 'Karachi',
        'units_donated' => 1,
        'notes'         => 'Test donation entry'
    ]);
    assertTest($donationId > 0, "DonationRecord::create inserted record ID {$donationId}");

    // 5. Verify total donations count incremented
    $reloadedAfterDonation = UserProfile::findByUserId($userId);
    assertTest((int)$reloadedAfterDonation['total_donations_count'] === $initialTotal + 1, "Donor total_donations_count incremented by 1");

    // 6. Test retrieval of donation history
    $history = DonationRecord::getByDonorId($userId);
    assertTest(!empty($history) && count($history) > 0, "DonationRecord::getByDonorId returns array");
    assertTest($history[0]['facility_name'] === 'Unit Test Blood Bank', "Verified facility_name of top record");

} catch (Exception $e) {
    echo "[FAIL] Unexpected Exception: " . $e->getMessage() . "\n";
    $failed++;
}

echo "========================================================\n";
echo "SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "========================================================\n";
exit($failed === 0 ? 0 : 1);
