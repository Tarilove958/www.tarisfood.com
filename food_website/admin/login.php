<?php
// admin/login.php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin') {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? AND user_type = 'admin' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_type'] = 'admin';
            redirect('index.php');
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700&family=Outfit:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#0066CC', secondary: '#DC3545', dark: '#212529' },
                    fontFamily: { heading: ['"Bricolage Grotesque"', 'sans-serif'], body: ['"Outfit"', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-dark flex items-center justify-center min-h-screen font-body p-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="font-heading text-3xl font-bold text-primary mb-2">Admin Portal</h1>
            <p class="text-gray-500">Secure Access Only</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-center text-sm font-bold border border-red-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-primary focus:ring-2 focus:ring-blue-100 outline-none" required>
            </div>
            <button type="submit" class="w-full bg-dark text-white font-bold py-3 rounded-xl hover:bg-black transition-colors">
                Enter Dashboard
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="../index.php" class="text-sm text-gray-400 hover:text-primary">← Back to Website</a>
        </div>
    </div>
</body>
</html>