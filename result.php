<?php session_start(); 

if(isset($_POST['btnulang']))
{
	session_destroy();
	header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Your Result</title>
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
	<br>
	<h1>~ RESULT ~</h1>
	<br>
<?php 

$koneksi = new mysqli("localhost","root","","fsp_uts");
	if($koneksi->connect_error){
		echo "Failed to connect MySQL: ".$koneksi->connect_error;
	}
	if(isset($_SESSION['answer']))
	{
		$skor = 0;
		$hasil = $_SESSION['answer'];
		foreach($hasil as $value)
		{
			foreach($value as $key2 => $value2)
			{
				$indikator = "";
				$jawaban = "SELECT j.benarkah as kunci, isi_jawaban as jawaban,s.pertanyaan as pertanyaan FROM jawaban as j inner join soal as s on j.idsoal=s.idsoal WHERE j.idsoal = $key2 and benarkah = 1";
				$kunci = $koneksi->query($jawaban);
				$kunjaw = $kunci->fetch_assoc();
				$pertanyaan = $kunjaw['pertanyaan'];
				$key = $kunjaw['jawaban'];
				if($value2 == $key){
					$indikator = "Benar";
				}else{
					$indikator = "Salah";
				}

				echo "<div class='bos'>";
				echo "<div class='anggota'>";
				echo "<p><br>".$key2.". ".$pertanyaan."</p>";

				if($indikator == 'Benar'){
					$skor++;
					echo "<p class='text-hijau'>";
				}else{
					echo "<p class='text-merah'>";
				}
				echo "Your Answer : ".$value2."</p>";
				echo "</div>";
				echo "</div>";
			}
		}

		$jumlahskor = $skor * 10;
		if($jumlahskor <= 50){
			echo "<h2>It's Okay! Let's Play Again!</h2>";
		}elseif($jumlahskor > 50 && $jumlahskor<=70){
			echo "<h2>Good Job!</h2>";
		}else{
			echo "<h2>Good Job! You Are Genius!</h2>";
		}
		echo "<h2>Your Score</h2>";
		echo "<h1>".$jumlahskor."</h1>";
	}
 ?>
 <form method="post" action="">
	 <div class="button">
	 	<button type="submit" name="btnulang" class="btn">PLAY AGAIN</button>
	 </div>
 </form>
</body>
</html>