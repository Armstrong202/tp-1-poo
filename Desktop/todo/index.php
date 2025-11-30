<?php
/* ------------------------------------------------ */
/* 1. CONFIGURATION & CONNEXION BDD */
/* ------------------------------------------------ */
define('DB_USER', 'root'); 
define('DB_PASS', ''); 
define('DB_NAME', 'todolist'); 
define('DB_HOST', '127.0.0.1'); 
define('DB_PORT', '3306'); 

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

/* ------------------------------------------------ */
/* 2. TRAITEMENT DES ACTIONS (Backend) */
/* ------------------------------------------------ */

// Ajouter une tâche
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (?)");
        $stmt->execute([$title]);
        header("Location: index.php"); // Évite la resoumission
        exit;
    }
}

// Supprimer une tâche
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// Récupérer les tâches
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma To-Do List | Projet PHP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #f39c12;
            --secondary: #2c3e50;
            --bg: #f5f5f5;
            --white: #ffffff;
            --danger: #e74c3c;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            background: var(--white);
            width: 100%;
            max-width: 500px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px;
        }

        header {
            background: var(--secondary);
            color: var(--white);
            padding: 30px;
            text-align: center;
        }

        header h1 { margin: 0; font-size: 1.8rem; }
        header p { margin: 5px 0 0; opacity: 0.8; font-size: 0.9rem; }

        /* Formulaire */
        .input-group {
            padding: 25px;
            background: #fdfdfd;
            border-bottom: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        input[type="text"] {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #eee;
            border-radius: 30px;
            outline: none;
            font-family: inherit;
            transition: 0.3s;
        }

        input[type="text"]:focus {
            border-color: var(--primary);
        }

        button.btn-add {
            background: var(--primary);
            color: white;
            border: none;
            width: 50px;
            height: 46px; /* Ajustement hauteur */
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        button.btn-add:hover { background: #e67e22; transform: scale(1.1); }

        /* Liste */
        ul { list-style: none; padding: 0; margin: 0; }

        li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 25px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        li:hover { background: #fafafa; }
        li:last-child { border-bottom: none; }

        .task-txt { font-weight: 500; }
        .task-date { font-size: 0.75rem; color: #999; margin-top: 4px; display: block;}

        .btn-delete {
            color: #ccc;
            text-decoration: none;
            font-size: 1.1rem;
            transition: 0.3s;
            padding: 5px;
        }

        .btn-delete:hover { color: var(--danger); transform: scale(1.2); }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <h1>Ma To-Do List</h1>
            <p>Gérez vos tâches simplement</p>
        </header>

        <form method="POST" action="" class="input-group">
            <input type="text" name="title" placeholder="Nouvelle tâche..." required autocomplete="off">
            <button type="submit" name="add_task" class="btn-add">
                <i class="fas fa-plus"></i>
            </button>
        </form>

        <ul>
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $row): ?>
                    <li>
                        <div>
                            <span class="task-txt"><?php echo htmlspecialchars($row['title']); ?></span>
                            <span class="task-date">
                                <i class="far fa-clock"></i> 
                                <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>
                            </span>
                        </div>
                        <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-check fa-3x"></i>
                    <p>Aucune tâche pour le moment !</p>
                </div>
            <?php endif; ?>
        </ul>
    </div>

</body>
</html>