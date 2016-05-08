<?php
class AE_Pdf_Creator extends AE_Base{
    public static $instance;
    /**
     * getInstance method
     *
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct(){

    }
    /**
     * init
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function init(){
        require_once dirname(__FILE__) . '/tcpdf_include.php';
    }
    /**
     * Generate a pdf file
     *
     * @param string $content
     * @return pdf file link
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function pdfGenarate($content, $file_name){
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 002');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        // ---------------------------------------------------------
        // set font
        $pdf->SetFont('times', 'R', 13);
        // add a page
        $pdf->AddPage();
        // set some text to print
        $txt = <<<EOD
            TCPDF Example 002
            Default page header and footer are disabled using setPrintHeader() and setPrintFooter() methods.
EOD;
// print a block of text using Write()
// ---------------------------------------------------------
//Close and output PDF document
        $pdf->writeHTML($content, true, false, true, false, '');
        ob_start();
        $pdf->Output(dirname(__FILE__).'/files/'.$file_name.'.pdf', 'F');
        $content = ob_get_clean();
        var_dump($content);
        $file_path = get_template_directory_uri().'/includes/modules/AE_PDF_Creator/file_1.pdf';
//============================================================+
// END OF FILE
//============================================================+
    return $file_path;
    }

}
