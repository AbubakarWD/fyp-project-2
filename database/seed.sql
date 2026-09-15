-- ============================================================================
-- BloodLife — Initial Seed Data
-- ============================================================================

-- 1. Seed Blood Groups with ABO/Rh Compatibility Matrices
INSERT INTO `blood_groups` (`id`, `code`, `display_name`, `can_donate_to`, `can_receive_from`, `description`) VALUES
(1, 'A+', 'A Positive', '["A+", "AB+"]', '["A+", "A-", "O+", "O-"]', 'Can donate to A+ and AB+. Can receive from A+, A-, O+, O-.'),
(2, 'A-', 'A Negative', '["A+", "A-", "AB+", "AB-"]', '["A-", "O-"]', 'Universal red cell donor for A and AB types.'),
(3, 'B+', 'B Positive', '["B+", "AB+"]', '["B+", "B-", "O+", "O-"]', 'Can donate to B+ and AB+. Can receive from B+, B-, O+, O-.'),
(4, 'B-', 'B Negative', '["B+", "B-", "AB+", "AB-"]', '["B-", "O-"]', 'Rare group. Can donate to B and AB types.'),
(5, 'AB+', 'AB Positive', '["AB+"]', '["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"]', 'Universal plasma donor and universal red cell recipient.'),
(6, 'AB-', 'AB Negative', '["AB+", "AB-"]', '["A-", "B-", "AB-", "O-"]', 'Rarest blood group in the general population.'),
(7, 'O+', 'O Positive', '["O+", "A+", "B+", "AB+"]', '["O+", "O-"]', 'Most common blood group. Essential for emergency transfusions.'),
(8, 'O-', 'O Negative', '["A+", "A-", "B+", "B-", "AB+", "AB-", "O+", "O-"]', '["O-"]', 'Universal Red Cell Donor. Critical for pediatric and trauma care.');

-- 2. Seed Demo Users (Default password for all demo accounts: Password123!)
-- Hash generated via password_hash('Password123!', PASSWORD_DEFAULT)
INSERT INTO `users` (`id`, `username`, `email`, `password`, `primary_role`, `status`) VALUES
(1, 'john_donor', 'donor1@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'donor', 'active'),
(2, 'sarah_requester', 'requester1@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'requester', 'active'),
(3, 'ahmed_donor', 'ahmed@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'donor', 'active'),
(4, 'fatima_donor', 'fatima@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'donor', 'active'),
(5, 'ali_requester', 'ali@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'requester', 'active'),
(6, 'zainab_donor', 'zainab@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'donor', 'active'),
(7, 'usman_both', 'usman@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'both', 'active'),
(8, 'maryam_donor', 'maryam@bloodlife.org', '$2y$10$gPGA/c1gnRSUCk/0QsuZCuezDqmeFV.vVdLZr2q.vDP5S6fuf/GFS', 'donor', 'active');

-- 3. Seed User Profiles
INSERT INTO `user_profiles` (`id`, `user_id`, `full_name`, `phone_number`, `gender`, `date_of_birth`, `blood_group_id`, `city`, `state_province`, `address`, `bio`, `is_available_donor`, `last_donated_at`, `total_donations_count`) VALUES
(1, 1, 'John Doe', '+12345678901', 'male', '1992-05-15', 7, 'Karachi', 'Sindh', 'Clifton Block 5, Karachi', 'Voluntary regular donor since 2018. Always ready for emergency calls.', 1, '2026-05-10', 6),
(2, 2, 'Sarah Jenkins', '+12345678902', 'female', '1995-08-22', 1, 'Lahore', 'Punjab', 'Gulberg III, Lahore', 'Community activist advocating for emergency blood availability.', 0, NULL, 0),
(3, 3, 'Ahmed Hassan', '+12345678903', 'male', '1988-11-04', 8, 'Islamabad', 'ICT', 'F-7/2, Islamabad', 'O Negative universal donor. Quick responder for pediatric cases.', 1, '2026-03-12', 12),
(4, 4, 'Fatima Noor', '+12345678904', 'female', '1997-03-19', 3, 'Rawalpindi', 'Punjab', 'Saddar, Rawalpindi', 'Registered voluntary donor.', 1, '2026-06-01', 3),
(5, 5, 'Ali Raza', '+12345678905', 'male', '1990-09-30', 5, 'Faisalabad', 'Punjab', 'D Ground, Faisalabad', 'Requester for local hospital emergency network.', 0, NULL, 0),
(6, 6, 'Zainab Bibi', '+12345678906', 'female', '1994-01-12', 2, 'Karachi', 'Sindh', 'DHA Phase 6, Karachi', 'A- negative donor. Glad to help anytime.', 1, '2026-01-20', 4),
(7, 7, 'Usman Khan', '+12345678907', 'male', '1991-07-08', 4, 'Lahore', 'Punjab', 'Model Town, Lahore', 'Both donor and requester for family network.', 1, '2026-04-15', 5),
(8, 8, 'Maryam Siddiqui', '+12345678908', 'female', '1998-12-05', 7, 'Islamabad', 'ICT', 'G-11/3, Islamabad', 'O+ regular donor. Healthy and fit.', 1, '2026-02-28', 2);

-- 4. Seed Emergency Blood Requests
INSERT INTO `blood_requests` (`id`, `requester_id`, `patient_name`, `blood_group_id`, `units_required`, `units_fulfilled`, `urgency_level`, `hospital_name`, `hospital_address`, `city`, `contact_number`, `required_date`, `medical_reason`, `status`) VALUES
(1, 2, 'Tariq Jenkins', 7, 2, 1, 'critical', 'Aga Khan University Hospital', 'Stadium Road, Karachi', 'Karachi', '+12345678902', '2026-09-18', 'Urgent cardiovascular surgery require 2 units O+ blood.', 'active'),
(2, 5, 'Bilal Raza', 8, 3, 0, 'high', 'Shaukat Khanum Hospital', 'Johar Town, Lahore', 'Lahore', '+12345678905', '2026-09-20', 'Oncology treatment support. Requires O- universal donor.', 'active'),
(3, 2, 'Zahra Ahmed', 1, 1, 1, 'medium', 'PIMS Hospital', 'Sector G-8/3, Islamabad', 'Islamabad', '+12345678902', '2026-09-10', 'Scheduled orthopaedic operation.', 'fulfilled');

-- 5. Seed Donor Responses
INSERT INTO `request_responses` (`id`, `request_id`, `donor_id`, `units_offered`, `status`, `donor_note`) VALUES
(1, 1, 1, 1, 'accepted', 'Available immediately in Karachi. Can reach hospital in 30 mins.'),
(2, 2, 3, 1, 'pending', 'O- donor here. Available to donate tomorrow morning in Lahore.');

-- 6. Seed Notifications
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `action_link`, `is_read`) VALUES
(1, 2, 'new_response', 'New Donor Response', 'John Doe responded to your blood request for Tariq Jenkins (O+).', '/requests.php', 0),
(2, 1, 'donor_match', 'Matching Emergency Request', 'Urgent O+ blood request created in Karachi at Aga Khan Hospital.', '/requests.php', 0),
(3, 3, 'donor_match', 'Matching Emergency Request', 'Urgent O- blood request created in Lahore at Shaukat Khanum Hospital.', '/requests.php', 0);
