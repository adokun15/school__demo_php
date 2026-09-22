<?php

require_once "database.php";

$sql = "SELECT faculty_id, name FROM faculties";

$result = $pdo->query($sql);

$faculties = $result->fetchAll(PDO::FETCH_ASSOC);

?>


<main class="registration-container">
        <h1>Course Registration</h1>
        <form method="POST">
            <!-- FACULTY -->
            <div class="form-group">
                <label for="faculty">Select Faculty</label>
                <select name="faculty_id" id="faculty">
                    <option value="">Select Faculty</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?= htmlspecialchars($faculty['faculty_id']) ?>">
                            <?= htmlspecialchars($faculty['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- DEPARTMENT -->
            <div class="form-group" id="department-container">
                <label for="department">
                    Select Department
                </label>
                <select name="department_id" id="department">
                    <option value="">
                        Select Department
                    </option>
                </select>
            </div>
            <button type="submit">
                Continue
            </button>

        </form>

    </main>


<script>

const facultySelect = document.getElementById("faculty");
const departmentContainer = document.getElementById("department-container");
const departmentSelect = document.getElementById("department");


facultySelect.addEventListener("change", async function () {

    const facultyId = this.value;

    // Nothing selected
    if (!facultyId) {
        departmentContainer.style.display = "none";
        departmentSelect.innerHTML = `
            <option value="">Select Department</option>
        `;

        return;
    }


    // Show department field
    departmentContainer.style.display = "block";


    // Temporary loading option
    departmentSelect.innerHTML = `
        <option value="">Loading departments...</option>
    `;


    try {

        const response = await fetch(
            `get_departments.php?faculty_id=${facultyId}`
        );

        const departments = await response.json();


        // Reset department select
        departmentSelect.innerHTML = `
            <option value="">Select Department</option>
        `;


        // Add departments
        departments.forEach(function (department) {

            const option = document.createElement("option");

            option.value = department.department_id;
            option.textContent = department.name;

            departmentSelect.appendChild(option);

        });

    } catch (error) {

        console.error(error);

        departmentSelect.innerHTML = `
            <option value="">Failed to load departments</option>
        `;

    }

});
</script>

