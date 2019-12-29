<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	function __construct() {
        parent::__construct();
    }

	public function index()
	{
		$data['myJS'] = 'welcome/myJS';
		$this->template->load('template', 'welcome/home', $data);
	}

	public function new_order()
	{
		include_once APPPATH . '/third_party/phpmailer/class.phpmailer.php';
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		$phone = $this->input->post('phone');
		$type = $this->input->post('type');
		$message = $this->input->post('message');
		$captcha = $_POST['g-recaptcha-response'];

		if(empty($name) || empty($email) || empty($phone) || empty($type)):
			$isValid = 0;
			$isPesan = "Mohon isi semua form, kecuali form pesan(opsional).";
		elseif(!$captcha):
			$isValid = 0;
			$isPesan = "Mohon cek CAPTCHA. Kami pastikan anda bukan robot.";
		else:
			$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6Ld0u8oUAAAAAEmqNV_uWLUcqKj226IYfF6fy2SD&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
			$responseData = json_decode($response);
			if ($responseData->success != true):
				$isValid = 0;
				$isPesan = "Kami pastikan anda bukan robot.";
			else:
				$content_order = "
					<html>
						<head>
							<title>FORM ORDER</title>
						</head>
						<body>
							<h3>FORM ORDER</h3>
							<table width='100%' border='1' align='left'>
								<tr>
									<th width='20%'>Waktu</th>
									<th width='20%'>Name</th>
									<th width='20%'>Email</th>
									<th width='20%'>Phone</th>
									<th width='20%'>Type</th>
									<th>Message</th>
								</tr>
								<tr>
									<td>".DATE_NOW."</td>
									<td>$name</td>
									<td>$email</td>
									<td>$phone</td>
									<td>$type</td>
									<td>$message</td>
								</tr>
							</table>
							<br>
							Regards<br>
							Support Multi Creative
						</body>
					</html>
				";
				$mail = new PHPMailer; 
				$mail->IsSMTP();
				$mail->SMTPSecure = 'ssl'; 
				$mail->Host = EMAIL_HOST;
				$mail->SMTPDebug = 2;
				$mail->Port = 465;
				$mail->SMTPAuth = true;
				$mail->Username = EMAIL_SUPPORT;
				$mail->Password = EMAIL_SUPPORT_KEY;
				$mail->SetFrom(EMAIL_SUPPORT, APP_NAME);
				$mail->Subject = "FORM ORDER $type";
				$mail->AddAddress(EMAIL_REYNALDI, EMAIL_REYNALDI_NAME);
				$mail->MsgHTML($content_order);
				$mail->Send();

				$arr = array(
					'name' => $name,
					'email' => $email,
					'phone' => $phone,
					'type' => $type,
					'message' => $message,
					'date' => DATE_NOW,
				);
				$insert = $this->db->insert(TB_MC_ORDER, $arr);
				if(!$insert):
					$isValid = 0;
					$isPesan = "Order gagal, terjadi karena masalah koneksi server";
				else:
					$isValid = 1;
					$isPesan = "Terimakasi sudah melakukan order di Multi Creative. Selanjutnya tim kami akan menghubungi Sdr/i $name";
				endif;
			endif;
		endif;

		$arr = array(
			'isValid' => $isValid,
			'isPesan' => $isPesan
		);
		echo json_encode($arr);
	}
}
