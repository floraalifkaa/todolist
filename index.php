<?php
	include 'database.php';

	// Proses insert data
	if(isset($_POST['add'])){
		$task = $_POST['task'];
		$deadline = $_POST['deadline'];
		$priority = $_POST['priority'];

		// Prepared statement
		$stmt = $conn->prepare("INSERT INTO tasks (tasklabel, taskstatus, deadline, priority) VALUES (?, 'open', ?, ?)");
		$stmt->bind_param("sss", $task, $deadline, $priority);

		if ($stmt->execute()) {
			// Tugas berhasil ditambahkan
			$_SESSION['success'] = "Tugas berhasil ditambahkan.";
			header('Location: index.php');
			exit();
		} else {
			// Terjadi kesalahan
			$_SESSION['error'] = "Gagal menambahkan tugas: " . $stmt->error;
			header('Location: index.php');
			exit();
		}

		$stmt->close();
	}

	// Ambil data untuk grafik (distribusi berdasarkan prioritas)
	$q_select_priority = "SELECT priority, COUNT(*) AS count FROM tasks GROUP BY priority";
	$run_q_select_priority = mysqli_query($conn, $q_select_priority);

	$priorities = ['high' => 0, 'medium' => 0, 'low' => 0]; // Array untuk menyimpan jumlah berdasarkan prioritas
	while ($row = mysqli_fetch_assoc($run_q_select_priority)) {
		$priorities[$row['priority']] = $row['count'];
	}

	// Proses show data
	$q_select = "SELECT * FROM tasks ORDER BY deadline ASC";
	$run_q_select = mysqli_query($conn, $q_select);

	// Proses delete data
	if(isset($_GET['delete'])){
		$q_delete = "DELETE FROM tasks WHERE taskid = '".$_GET['delete']."' ";
		$run_q_delete = mysqli_query($conn, $q_delete);
		header('Refresh:0; url=index.php');
	}

	// Proses update data (close or open)
	if(isset($_GET['done'])){
		$status = ($_GET['status'] == 'open') ? 'close' : 'open';
		$q_update = "UPDATE tasks SET taskstatus = '".$status."' WHERE taskid = '".$_GET['done']."' ";
		$run_q_update = mysqli_query($conn, $q_update);
		header('Refresh:0; url=index.php');
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>To Do List</title>
	<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
		* {
			padding:0;
			margin:0;
			box-sizing: border-box;
		}
		body {
			font-family: 'Roboto', sans-serif;
			background: linear-gradient(to right, #8f94fb, #4e54c8);
			color: white;
			text-align: center;
			padding: 20px;
		}
		.container {
			width: 590px;
			margin: auto;
			background: white;
			padding: 20px;
			border-radius: 10px;
			color: black;
		}
		.card {
			background-color: #fff;
			padding:15px;
			border-radius: 5px;
			margin-bottom: 10px;
		}
		.input-control {
			width:100%;
			padding:0.5rem;
			font-size: 1rem;
			margin-bottom: 10px;
		}
		.text-right {
			text-align: right;
		}
		button {
			padding: 0.5rem 1rem;
			font-size: 1rem;
			cursor: pointer;
			background: #4e54c8;
			color: #fff;
			border: none;
			border-radius: 3px;
		}
		.task-item {
			display: flex;
			justify-content: space-between;
			padding: 10px;
			border-bottom: 1px solid #ddd;
		}
		.task-item.done span {
			text-decoration: line-through;
			color: #ccc;
		}
		.task-meta {
			font-size: 14px;
			color: gray;
		}
		.priority-high { color: red; }
		.priority-medium { color: orange; }
		.priority-low { color: green; }
	</style>
</head>
<body>

	<div class="container">
		<h2>Aplikasi To Do List</h2>
		<p><?= date("l, d M Y") ?></p>

		<div class="card">
			<form action="" method="post">
				<input type="text" name="task" class="input-control" placeholder="Tambahkan Tugas" required>
				<input type="date" name="deadline" class="input-control" required>
				<select name="priority" class="input-control" required>
					<option value="high">Tinggi</option>
					<option value="medium">Sedang</option>
					<option value="low">Rendah</option>
				</select>
				<div class="text-right">
					<button type="submit" name="add">Tambah</button>
				</div>
			</form>
		</div>

		<h3>Daftar Tugas</h3>
		<?php if(mysqli_num_rows($run_q_select) > 0) { ?>
			<?php while($r = mysqli_fetch_array($run_q_select)) { ?>
				<div class="card">
					<div class="task-item <?= $r['taskstatus'] == 'close' ? 'done':'' ?>">
						<div>
							<input type="checkbox" onclick="window.location.href = '?done=<?= $r['taskid'] ?>&status=<?= $r['taskstatus'] ?>'" <?= $r['taskstatus'] == 'close' ? 'checked':'' ?>>
							<span><?= $r['tasklabel'] ?></span>
							<div class="task-meta">
								Deadline: <?= date("d M Y", strtotime($r['deadline'])) ?> |
								Prioritas: <span class="priority-<?= $r['priority'] ?>"><?= ucfirst($r['priority']) ?></span>
							</div>
						</div>
						<div>
							<a href="edit.php?id=<?= $r['taskid'] ?>" title="Edit"><i class="bx bx-edit"></i></a>
							<a href="?delete=<?= $r['taskid'] ?>" title="Hapus" onclick="return confirm('Apakah Anda yakin?')"><i class="bx bx-trash"></i></a>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<p>Belum ada tugas</p>
		<?php } ?>

		<!-- Grafik Prioritas (Pie Chart) -->
		<h3>Grafik Prioritas Tugas</h3>
		<canvas id="taskPriorityChart" width="400" height="200"></canvas>
		<script>
			var ctx = document.getElementById('taskPriorityChart').getContext('2d');
			var taskPriorityChart = new Chart(ctx, {
				type: 'pie', // Menggunakan tipe pie chart
				data: {
					labels: ['Tinggi', 'Sedang', 'Rendah'], // Label untuk kategori
					datasets: [{
						label: 'Jumlah Tugas',
						data: [<?= $priorities['high'] ?>, <?= $priorities['medium'] ?>, <?= $priorities['low'] ?>], // Data berdasarkan prioritas
						backgroundColor: ['red', 'orange', 'green'], // Warna untuk setiap bagian pie
						borderColor: ['darkred', 'darkorange', 'darkgreen'], // Border warna
						borderWidth: 1
					}]
				},
				options: {
					responsive: true
				}
			});
		</script>

	</div>

</body>
</html>
