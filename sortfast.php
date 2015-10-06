<html>
	<head>
		<title>Telephones</title>
	</head>
	<body>
		<form enctype="multipart/form-data" action="" method="post">
			<div style="float:left">
				Insert data: 
				<input type="text" name="data" placeholder="Name, E-mail, Telephone, ..."/><input type="submit" name="search" value="Search"/>
			</div>
			<div style="float:right">
				<input type="checkbox" name="datos[]" value="Name" checked> Name<br>
				<input type="checkbox" name="datos[]" value="Telephone" checked> Telephone<br>
				<input type="checkbox" name="datos[]" value="Email" checked> Email<br>
				<input type="checkbox" name="datos[]" value="Department"> Department<br>
				<input type="checkbox" name="datos[]" value="Description"> Description<br>
			</div>
			<div style="clear:left">
				<br/>Sort by: 
				<select name="order">
					<option value="cn" selected>Name</option>
					<option value="telephonenumber">Telephone</option>
					<option value="mail">E-mail</option>
					<option value="department">Department</option>
					<option value="description">Description</option>
				</select>
			</div>
		</form>
		<?php 
		$server = "ldap://192.168.101.249";
			$user = "actived@magni.local";
			$psw = "********";
			$dn = "OU=MAGNI Users,DC=magni,DC=local";
			$search = "CN=*";

			$ds=ldap_connect($server);
			$r=ldap_bind($ds, $user , $psw); 
			$sr=ldap_search($ds, $dn, $search);
			if(isset($_POST['search'])){
				$time_start = microtime(true);
				$dato = $_POST['data'];
				$datos = $_POST['datos'];
				$order = $_POST['order'];

				ldap_sort($ds,$sr,$order);
				$data = ldap_get_entries($ds, $sr);

				function IsChecked($chkname,$value){
					if(!empty($_POST[$chkname])){
						foreach($_POST[$chkname] as $chkval){
							if($chkval == $value){
								return true;
							}
						}
					}
					return false;
				}
					
				$num_fila = 0; 
				echo "<table border=1 align=center>";
				echo "<tr bgcolor=\"bbbbbb\" align=center>";
				$name = 0;
				$telephone = 0;
				$mail = 0;
				$department = 0;
				$description = 0;
				if(IsChecked('datos','Name')){
					$name = 1;
					echo "<th width=180px>Name</th>";
				} 
				if(IsChecked('datos','Telephone')){
					$telephone = 1;
					echo "<th width=100px>Telephone</th>";
				} 
				if(IsChecked('datos','Email')){ 
					$mail = 1;
					echo "<th width=180px>E-mail</th>";
				}
				if(IsChecked('datos','Department')){ 
					$department = 1;
					echo "<th width=180px>Department</th>";
				}
				if(IsChecked('datos','Description')){
					$description = 1;
					echo "<th width=180px>Description</th>";
				}
				echo "</tr>";

				for ($i=0; $i<$data["count"]; $i++) {
					if (((isset($data[$i]["telephonenumber"][0]))or(isset($data[$i]["mail"][0])))&&
						((isset($data[$i]["cn"][0])&&preg_match('/'.strtolower($dato).'/',strtolower($data[$i]["cn"][0]))) or 
						(isset($data[$i]["description"][0])&&preg_match('/'.strtolower($dato).'/',strtolower($data[$i]["description"][0]))) or 
						(isset($data[$i]["department"][0])&&preg_match('/'.strtolower($dato).'/',strtolower($data[$i]["department"][0]))) or 
						(isset($data[$i]["telephonenumber"][0])&&preg_match('/'.$dato.'/',$data[$i]["telephonenumber"][0])) or 
						(isset($data[$i]["mail"][0])&&preg_match('/'.strtolower($dato).'/',strtolower($data[$i]["mail"][0]))))){
						
						//$user = $data[$i]["cn"][0];
						//$user_info=$ds->info($user,array("useraccountcontrol"));
						//$enabled = (($user_info[0]['useraccountcontrol'][0] & 2) == 0);
						//echo "Account Status: ".$data[$i]["useraccountcontrol"][0]."<br>\n";
						if (isset($data[$i]["useraccountcontrol"][0])) {
						if (($data[$i]["useraccountcontrol"][0] & 2) == 0) {
						//if ((($data[$i]["useraccountcontrol"][0] != 546))) {
						

						echo "<tr "; 
						if ($num_fila%2==0) 
							echo "bgcolor=#F2F2F2";
						else 
							echo "bgcolor=#F6CECE";
						echo ">";

						if($name == 1) echo "<td>" . $data[$i]["cn"][0] . "</td>";
							
						if($telephone == 1) {
							if (isset($data[$i]["telephonenumber"][0])) 
							echo "<td>" . $data[$i]["telephonenumber"][0] . "</td>";
							else echo "<td></td>";
						}
							
						if($mail == 1) {
							if (isset($data[$i]["mail"][0])){
								$email=$data[$i]["mail"][0];
								echo "<td><a href=\"mailto:$email\" target=\"_top\">" . $data[$i]["mail"][0] . "</td>";
							} else echo "<td></td>";
						}

						if($department == 1) {
							if (isset($data[$i]["department"][0])) 
							echo "<td>" . $data[$i]["department"][0] . "</td>";
							else echo "<td></td>";
						}

						if($description == 1) {
							if (isset($data[$i]["description"][0])) 
							echo "<td>" . $data[$i]["description"][0] . "</td>";
							else echo "<td></td>";
						}
							
						echo "</tr>";
						$num_fila++;
						}}
					}else echo "";
				}
				echo "<div style=\"float:left\">Found ".$num_fila." entries</div><br/><br/><br/></table>";
			}
			ldap_close($ds);
		?>
	</body>
</html>