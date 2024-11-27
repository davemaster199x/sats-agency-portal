<?php

class SASPDF extends FPDF{

	protected $CI;

	function Header()
	{
		$this->CI =& get_instance();

		$headerText = $this->headerText;
		
		// if( $this->CI->session->country_id == 1 ){ // AU
		// 	$image = '/images/pdf/sats_letterhead_au.png';
		// }else if( $this->CI->session->country_id == 2 ){ // NZ
		// 	$image = '/images/pdf/sats_letterhead_nz.png';
		// }
		//$this->Image($_SERVER['DOCUMENT_ROOT'] . '/images/pdf/sats_header.png',150,10,50);
		// $this->Image($_SERVER['DOCUMENT_ROOT'] . $image,0,0,210);

        $this->SetFillColor(0, 96, 128); // RGB color for blue

        // Create a filled rectangle for the header background
        $this->Rect(0, 0, 210, 30, 'F');
        // Set text color to white
        $this->SetTextColor(255, 255, 255);
        
		$this->Image(FCPATH . "theme/sas/images/pdf/logos.png",10,4,70);
		
		// Title
        
		$this->SetY(15); // Adjust the vertical position for text
        $this->SETX(110);
        $this->Cell(30); // Move to the right
        $this->SetFont('Arial', 'B', 15); // Set font for the header text
        $this->Cell(0, 5, $headerText, 0, 0, 'C'); // Adjust the text and alignment
        $this->Ln(20);

	
	}

	function Footer()
	{

		// page number
		$this->SetY(-10);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Page '.$this->PageNo().' of {nb}',0,0,'C');


		// get logged user
		$params = array(
			'aua_id' => $this->CI->session->aua_id,
			'sel_query' => '
				aua.`agency_user_account_id`,
				aua.`fname`,
				aua.`lname`,
				aua.`photo`,
				aua.`email`,
				aua.`user_type`,
				aua.`phone`,
				aua.`job_title`,
				aua.`active`
			'
		);

		// get logged user
		$user_sql = $this->CI->user_accounts_model->get_user_accounts($params);
		$user = $user_sql->row();

		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,"{$user->fname} {$user->lname} ".date('d/m/Y H:i'),0,0,'R');

		if( $this->CI->session->country_id == 1 ){ // AU
			$image = '/images/pdf/sats_footer_au.png';
		}else if( $this->CI->session->country_id == 2 ){ // NZ
			$image = '/images/pdf/sats_footer_nz.png';
		}
		//$this->Image($_SERVER['DOCUMENT_ROOT'] . $image,0,273,210);
		$this->Image(FCPATH . "theme/sas/images/pdf/footer.png",0,273,210);
	}

}

