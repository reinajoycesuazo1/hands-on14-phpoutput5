<?php
// Database connection - replace with your credentials
$conn = new mysqli('localhost', 'root', '', 'faculty');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create or Update
    $data = [
        'first_name' => $_POST['first_name'],
        'middle_name' => $_POST['middle_name'],
        'last_name' => $_POST['last_name'],
        'age' => (int)$_POST['age'],
        'gender' => $_POST['gender'],
        'address' => $_POST['address'],
        'position' => $_POST['position'],
        'salary' => (float)$_POST['salary']
    ];

    if (isset($_POST['create'])) {
        // Create new faculty
        $stmt = $conn->prepare("INSERT INTO faculty (first_name, middle_name, last_name, age, gender, address, position, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssisssd", ...array_values($data));
        $stmt->execute();
    } elseif (isset($_POST['update'])) {
        // Update existing faculty
        $data['id'] = (int)$_POST['id'];
        $stmt = $conn->prepare("UPDATE faculty SET first_name=?, middle_name=?, last_name=?, age=?, gender=?, address=?, position=?, salary=? WHERE id=?");
        $stmt->bind_param("sssisssdi", ...array_values($data));
        $stmt->execute();
    }
} elseif (isset($_GET['delete'])) {
    // Delete faculty
    $conn->query("DELETE FROM faculty WHERE id=" . (int)$_GET['delete']);
}

// Get all faculty members
$faculty = $conn->query("SELECT * FROM faculty")->fetch_all(MYSQLI_ASSOC);
$edit_member = isset($_GET['edit']) ? $faculty[array_search($_GET['edit'], array_column($faculty, 'id'))] : null;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Management</title>
    <!-- Material Design Lite CSS -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
        <header class="mdl-layout__header">
            <div class="mdl-layout__header-row">
                <span class="mdl-layout-title">Faculty Management System</span>
            </div>
        </header>
        <main class="mdl-layout__content">
            <div class="page-content">
    
                <!-- Faculty Form Card -->
                <div class="mdl-card mdl-shadow--2dp">
                    <div class="mdl-card__title">
                        <h2 class="mdl-card__title-text"><?= isset($edit_member) ? 'Edit Faculty' : 'Add New Faculty' ?></h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <form method="post" class="form-grid">
                            <input type="hidden" name="id" value="<?= $edit_member['id'] ?? '' ?>">
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" name="first_name" 
                                       value="<?= $edit_member['first_name'] ?? '' ?>" required>
                                <label class="mdl-textfield__label">First Name*</label>
                            </div>
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" name="middle_name" 
                                       value="<?= $edit_member['middle_name'] ?? '' ?>">
                                <label class="mdl-textfield__label">Middle Name</label>
                            </div>
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" name="last_name" 
                                       value="<?= $edit_member['last_name'] ?? '' ?>" required>
                                <label class="mdl-textfield__label">Last Name*</label>
                            </div>
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="number" name="age" 
                                       min="18" max="100" value="<?= $edit_member['age'] ?? '' ?>" required>
                                <label class="mdl-textfield__label">Age*</label>
                            </div>
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <select class="mdl-textfield__input" name="gender" required>
                                    <option value=""></option>
                                    <option value="Male" <?= ($edit_member['gender'] ?? '') == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= ($edit_member['gender'] ?? '') == 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= ($edit_member['gender'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                                <label class="mdl-textfield__label">Gender*</label>
                            </div>
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label full-width">
                                <textarea class="mdl-textfield__input" name="address" required><?= $edit_member['address'] ?? '' ?></textarea>
                                <label class="mdl-textfield__label">Address*</label>
                            </div>
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="text" name="position" 
                                       value="<?= $edit_member['position'] ?? '' ?>" required>
                                <label class="mdl-textfield__label">Position*</label>
                            </div>
                            
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <input class="mdl-textfield__input" type="number" name="salary" 
                                       step="0.01" min="0" value="<?= $edit_member['salary'] ?? '' ?>" required>
                                <label class="mdl-textfield__label">Salary*</label>
                            </div>
                            
                            <div class="full-width" style="text-align: right;">
                                <?php if (isset($edit_member)): ?>
                                    <a href="index.php" class="mdl-button mdl-js-button mdl-button--raised">Cancel</a>
                                <?php endif; ?>
                                <button type="submit" name="<?= isset($edit_member) ? 'update' : 'create' ?>" 
                                        class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored">
                                    <?= isset($edit_member) ? 'Update' : 'Create' ?> Faculty
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Faculty List -->
                <div class="mdl-card mdl-shadow--2dp">
                    <div class="mdl-card__title">
                        <h2 class="mdl-card__title-text">Faculty Members</h2>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <?php if (empty($faculty)): ?>
                            <p>No faculty members found.</p>
                        <?php else: ?>
                            <table class="mdl-data-table mdl-js-data-table mdl-shadow--2dp">
                                <thead>
                                    <tr>
                                        <th class="mdl-data-table__cell--non-numeric">ID</th>
                                        <th class="mdl-data-table__cell--non-numeric">Full Name</th>
                                        <th>Age</th>
                                        <th class="mdl-data-table__cell--non-numeric">Gender</th>
                                        <th class="mdl-data-table__cell--non-numeric">Position</th>
                                        <th>Salary</th>
                                        <th class="mdl-data-table__cell--non-numeric">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faculty as $member): ?>
                                    <tr>
                                        <td class="mdl-data-table__cell--non-numeric"><?= $member['id'] ?></td>
                                        <td class="mdl-data-table__cell--non-numeric"><?= $member['first_name'] . ' ' . ($member['middle_name'] ? $member['middle_name'] . ' ' : '') . $member['last_name'] ?></td>
                                        <td><?= $member['age'] ?></td>
                                        <td class="mdl-data-table__cell--non-numeric"><?= $member['gender'] ?></td>
                                        <td class="mdl-data-table__cell--non-numeric"><?= $member['position'] ?></td>
                                        <td><?= number_format($member['salary'], 2) ?></td>
                                        <td class="mdl-data-table__cell--non-numeric">
                                            <a href="index.php?edit=<?= $member['id'] ?>" class="mdl-button mdl-js-button mdl-button--icon">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <a href="index.php?delete=<?= $member['id'] ?>" class="mdl-button mdl-js-button mdl-button--icon" onclick="return confirm('Are you sure?')">
                                                <i class="material-icons">delete</i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Floating Action Button -->
    <a href="index.php" class="mdl-button mdl-js-button mdl-button--fab mdl-button--colored fab">
        <i class="material-icons">add</i>
    </a>
    
    <!-- Material Design Lite JS -->
    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
