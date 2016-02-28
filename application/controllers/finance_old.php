<?php class Finance extends CI_Controller {

    function Finance() {
        parent::__construct();
        $this->load->model('finance_model');
        //$this->load->view('finance/feedback');
        $this->finance_model->setup_gocardless();
        $this->page_info = array(
            'id' => 27,
            'title' => 'Finance',
            'big_title' => '<span class="big-text-small">My Finances</span>',
            'description' => 'JCR, Sports and Society finances',
            'requires_login' => TRUE,
            'allow_non-butler' => FALSE,
            'require-secure' => TRUE,
            'css' => array('finance/finance', 'finance/notifications/notifications'),
            'js' => array('finance/finance', 'finance/invoices/invoices', 'finance/notifications/notifications'),
            'keep_cache' => FALSE,
            'editable' => TRUE
        );
        if($this->uri->segment(2) == 'view_claim' && $this->finance_model->finance_permissions()){
            $id = $this->uri->segment(3);
            $claim = $this->finance_model->get_claim($id);
            $this->page_info['title'] .= ' - '.$this->uri->segment(3).' - '.$claim['pay_to'];
        }
    }

    
    
    
    function sortcode(){
        $key = 'b1f9d5e9b273812925b63f5b840526ce';
        $password = 'aBcDeFgH1!';
        $sortcode = $this->input->post('sortcode');
        $account = '';
        $url = 'https://www.bankaccountchecker.com/listener.php?key='.$key.'&password='.$password.'&output=json&type=uk&sortcode='.$sortcode.'&bankaccount='.$account;
        $data = file_get_contents($url);
        $this->load->view('finance/claims/sortcode', array('data'=>$data));
    }
    
    
    
    function claim_pdf(){
    
        if($this->finance_model->finance_permissions()){
    
            $this->load->library(array('pdf', 'pdfi'));
            $this->load->helper('download');
            $this->pdfi->fontpath = 'application/font/';
            define('FPDF_FONTPATH','application/font/');
            
            $claim_id = $this->uri->segment(3);
            $claim = $this->finance_model->get_claim($claim_id);
            
            $page_width = 188;
            $page_height = 260;
            
            $this->pdfi->FPDF();
            $this->pdfi->AddPage();
            $this->pdfi->SetFont('Arial', '', 14);
            $this->pdfi->SetTextColor(0,0,0);
            
            // Write something
            $this->pdfi->Image('application\views\finance\claims/logo.png', $this->pdfi->GetX(), $this->pdfi->GetY(), $page_width/4);
            $this->pdfi->Cell($page_width/4, 60, '', 0, 0);
            $this->pdfi->SetFont('Arial', '', 30);
            $this->pdfi->Cell($page_width/2, 60, 'JCR Claims Form', 0, 0, 'C');
            $this->pdfi->Image('application\views\finance\claims/logo.png', $this->pdfi->GetX(), $this->pdfi->GetY(), $page_width/4);
            $this->pdfi->Cell($page_width/4, 60, '', 0, 1);
            $this->pdfi->Ln(10);
            
            $this->pdfi->SetFont('Arial', '', 14);
            $this->pdfi->Cell($page_width/2, 7, 'Pay: '.$claim['pay_to'], 1, 0);
            $this->pdfi->Cell($page_width/2, 7, 'The Sum Of: '.chr(163).$claim['amount'], 1, 1);
            $this->pdfi->Cell($page_width, 7, 'Item: '.$claim['item'], 1, 1);
            $this->pdfi->Cell($page_width/2, 7, 'Budget: '.$claim['budget_name'], 1, 0);
            $this->pdfi->Cell($page_width/2, 7, 'Budget Holder: '.($claim['prefname']==''?$claim['firstname']:$claim['prefname']).' '.$claim['surname'], 1, 1);
            $this->pdfi->Ln(10);
            
            $this->pdfi->SetFont('Arial', 'B', 24);
            $this->pdfi->Write(6, 'Details:');
            $this->pdfi->Ln(10);
            $this->pdfi->SetFont('Arial', '', 12);
            $this->pdfi->Write(6, $claim['details']);
            
            $this->pdfi->SetY(-77);
            $this->pdfi->SetFont('Arial','I',8);
            $this->pdfi->Write(6, 'For JCR Treasurer Use:');
            $this->pdfi->Ln(5);
            $this->pdfi->SetFont('Arial','',12);
            $this->pdfi->Cell($page_width/2, 7, 'Paid On: ', 0, 0, 'R');
            $this->pdfi->Cell($page_width/2, 7, '', 1, 1);
            $this->pdfi->Cell($page_width/2, 7, 'Cheque Number: ', 0, 0, 'R');
            $this->pdfi->Cell($page_width/2, 7, '', 1, 1);
            
            $this->pdfi->Cell($page_width/3, 30, '', 1, 0);
            $this->pdfi->Cell($page_width/3, 30, '', 1, 0);
            $this->pdfi->Cell($page_width/3, 30, '', 1, 1);
            $this->pdfi->Cell($page_width/3, 7, 'JCR President', 1, 0, 'C');
            $this->pdfi->Cell($page_width/3, 7, 'JCR Treasurer', 1, 0, 'C');
            $this->pdfi->Cell($page_width/3, 7, 'College Bursar', 1, 1, 'C');
            
            $files = explode(',', $claim['files'],-1);
            $n = 1;
            $xmin = $this->pdfi->GetX();
            foreach($files as $f){
                if(strpos($f, '.pdf') === FALSE){
                    $this->pdfi->AddPage();
                    $size = getimagesize('application/views/finance/files/'.$f);
                    if($size[0]/$size[1] > 1/sqrt(2)){
                        if($size[0] > 700){
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10, -$size[0] * 25.4/$page_width);
                        }else{
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10);
                        }
                    }else{
                        if($size[1] > 900){
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10, -$size[1] * 25.4/$page_height, -$size[1] * 25.4/$page_height);
                        }else{
                            $this->pdfi->Image('application/views/finance/files/'.$f, 10, 10);
                        }
                    }
                }else{
                    $pagecount = $this->pdfi->setSourceFile('application/views/finance/files/'.$f);  
                    for($i=0; $i<$pagecount; $i++){
                        $this->pdfi->AddPage();
                        $tplidx = $this->pdfi->importPage($i+1, '/MediaBox');
                        $this->pdfi->useTemplate($tplidx, 10, 10, 200); 
                    }
                }                
            }
                        
            // Output
            $filename = VIEW_PATH.'finance/claims/temp_pdf/claim_'.$claim_id.'.pdf';
            $this->pdfi->Output($filename, 'F');
            $this->output->set_content_type('application/pdf');
            $data = file_get_contents($filename);
            force_download('claim_'.$claim_id.'.pdf', $data);
        
        }
    }
    
    

}

/* End of file finance.php */
/* Location: ./application/controllers/finance.php */