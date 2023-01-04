 <?php
	session_start();

	//session_destroy();
	$koneksi = new mysqli("localhost","root","","fsp_uts");
	if($koneksi->connect_error){
		echo "Failed to connect MySQL: ".$koneksi->connect_error;
	}
	if(!isset($_SESSION['answer'])){
		$_SESSION['answer'] = [];
	}
	if(!isset($_SESSION['rdojawaban'])) {
		$_SESSION['rdojawaban'] = [];
	}

	//Menyimpan kedalam array session
	if(isset($_POST['jawaban'])){
		array_push($_SESSION['answer'],$_POST['jawaban']);
		array_push($_SESSION['rdojawaban'], $_POST['jawaban']);
	}

	//Mengambil halaman terbesar
	$maxSoal = "SELECT MAX(halaman_ke) as MaxPage FROM soal";
	$hasilmaxSoal = $koneksi->query($maxSoal);
	$hasil_maxSoal = $hasilmaxSoal->fetch_assoc();

	//Mengambil halaman terkecil
	$minSoal = "SELECT MIN(halaman_ke) as MinPage FROM soal";
	$hasilminSoal = $koneksi->query($minSoal);
	$hasil_minSoal = $hasilminSoal->fetch_assoc();

	$aktif = (isset($_POST['aktif']))? $_POST['aktif']:$hasil_minSoal['MinPage'];
	if(isset($_POST['next'])){
    	$aktif++;
	}

	if(isset($_POST['prev'])){
    	$aktif--;
    	array_splice($_SESSION['answer'], -2);
	}

	if(isset($_POST['finish'])){
    	header("location: result.php?");
	} 

	$soal_now = "SELECT * FROM soal WHERE halaman_ke=$aktif";
	$hasil_now = $koneksi->query($soal_now);
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>QUIZ HAPPY</title>
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<form method="post" action="">
	<input type="hidden" name="aktif" value="<?= $aktif ?>">
	<div class="container">
		<h1>~ QUIZ HAPPY ~</h1>
		<?php 
		while($result = $hasil_now->fetch_assoc()):
			$jawaban = [];
			$sqlJawaban = "SELECT * FROM jawaban WHERE idsoal = ".$result['idsoal'];
			$hasilJawaban = $koneksi->query($sqlJawaban);
			echo "<div class='container'>";
				echo "<div class='child'>";
					echo "<div class='content'>";
					echo "<h3>".$result['nomor'].". ".$result['pertanyaan']."</h3>";
					while($result2 = $hasilJawaban->fetch_assoc()){
						array_push($jawaban,$result2['isi_jawaban']);
					}
				shuffle($jawaban);
				for($i=0;$i<count($jawaban);$i++):
					$checked = "false";
                    if(isset($_SESSION['rdojawaban'])){
                        $cek = $_SESSION['rdojawaban'];
                        foreach ($cek as $value) {
                            foreach ($value as $key2 => $value2) {
                                if($key2 == $result['nomor']){
                                    if($value2 == $jawaban[$i]){
                                        $checked = "true";
                                    }
                                }
                            }
                        }
                    } 
					
                    if($checked=="true"):?>
                     	<h3><label><input type="radio" name="jawaban[<?= $result['nomor'] ?>]" value="<?= $jawaban[$i] ?>" required checked><?= $jawaban[$i] ?></label></h3>
                    <?php endif;

                    if($checked=="false"):?>
                        <h3><label><input type="radio" name="jawaban[<?= $result['nomor'] ?>]" value="<?= $jawaban[$i] ?>" required><?= $jawaban[$i] ?></label></h3>
                     
                    <?php endif; ?>
				<?php endfor;?>
				<?php 
					echo "</div>";
				echo "</div>";
			echo "</div>";
			 ?>
	<?php endwhile;
    ?>
 	<div class="container">
 		<?php if($aktif>$hasil_minSoal['MinPage']):?>
    		<div class="button">
				<input type="submit" name="prev" value="PREVIOUS" class="btn">
    		</div>
			<br>
  		<?php endif; ?>
		<?php if($aktif<$hasil_maxSoal['MaxPage']):?>
    		<div class="button">
				<input type="submit" name="next" value="NEXT" class="btn">
    		</div>
			<br>
		<?php endif; ?>
		<?php if($aktif==$hasil_maxSoal['MaxPage']):?>
    		<div class="button">
				<input type="submit" name="finish" value="SUBMIT" class="btn">
    		</div>
    	<?php endif;?>
 	</div>
</div>
</form>
</body>
</html>