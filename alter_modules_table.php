<?php
require_once 'config.php';
require_once 'core/db.php'; // Assuming db.php handles the $conn connection

echo "Attempting to alter 'modules' table...\n";

try {
    // Check if 'slug' column exists
    $check_column_sql = "SHOW COLUMNS FROM `modules` LIKE 'slug'";
    $result = $conn->query($check_column_sql);

    if ($result->num_rows > 0) {
        // If 'slug' column exists, drop it to ensure a clean slate for re-creation
        $drop_column_sql = "ALTER TABLE `modules` DROP COLUMN `slug`";
        if ($conn->query($drop_column_sql) === TRUE) {
            echo "Existing 'slug' column dropped from 'modules' table.\n";
        } else {
            echo "Error dropping existing 'slug' column: " . $conn->error . "\n";
            // If dropping fails, we can't proceed safely, so exit
            $conn->close();
            exit();
        }
    }

    // Add the 'slug' column (without UNIQUE initially)
    $add_column_sql = "ALTER TABLE `modules` ADD COLUMN `slug` VARCHAR(255) NOT NULL AFTER `name`";
    if ($conn->query($add_column_sql) === TRUE) {
        echo "Column 'slug' added to 'modules' table successfully (without UNIQUE).\n";

        // Populate the 'slug' column for existing entries, ensuring uniqueness with ID
        $modules_result = $conn->query("SELECT id, name FROM modules");
        if ($modules_result) {
            while ($row = $modules_result->fetch_assoc()) {
                $current_id = $row['id'];
                $current_name = $row['name'];

                // Generate a base slug
                $base_slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim($current_name, '-')));
                if (empty($base_slug)) {
                    $base_slug = 'module'; // Default if name results in empty slug
                }

                // Ensure uniqueness by appending ID
                $final_slug = $base_slug . '-' . $current_id;

                $update_slug_sql = $conn->prepare("UPDATE modules SET slug = ? WHERE id = ?");
                $update_slug_sql->bind_param("si", $final_slug, $current_id);
                if ($update_slug_sql->execute()) {
                    // echo "Updated slug for module ID {$current_id} to '{$final_slug}'.\n";
                } else {
                    echo "Error updating slug for module ID {$current_id}: " . $update_slug_sql->error . "\n";
                }
                $update_slug_sql->close();
            }
            echo "All 'slug' values populated and made unique.\n";

            // Now add the UNIQUE constraint
            $add_unique_constraint_sql = "ALTER TABLE `modules` ADD UNIQUE (`slug`)";
            if ($conn->query($add_unique_constraint_sql) === TRUE) {
                echo "UNIQUE constraint added to 'slug' column successfully.\n";
            } else {
                echo "Error adding UNIQUE constraint to 'slug' column.\n";
            }

        } else {
            echo "Error fetching modules for slug population: " . $conn->error . "\n";
        }
    } else {
        echo "Error adding 'slug' column: " . $conn->error . "\n";
    }

} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

$conn->close();
echo "Module table alteration script finished.\n";
?>