-- Add submission_id column to activities table for direct linking
ALTER TABLE `activities` 
ADD COLUMN `submission_id` INT(11) NULL AFTER `activity_type`,
ADD INDEX `idx_submission_id` (`submission_id`);

-- Add foreign key constraint to ensure data integrity
ALTER TABLE `activities`
ADD CONSTRAINT `fk_activities_submissions`
FOREIGN KEY (`submission_id`) REFERENCES `submissions`(`submission_id`)
ON DELETE CASCADE ON UPDATE CASCADE;
