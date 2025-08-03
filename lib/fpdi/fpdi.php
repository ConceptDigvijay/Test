<?php
/**
 * FPDI - Simple PDF import class for use with FPDF
 * Simplified version for the Ganpati donation project
 */

require_once 'fpdf.php';

class FPDI extends FPDF
{
    protected $templates = array();
    
    function __construct($orientation='P', $unit='mm', $size='A4')
    {
        parent::__construct($orientation, $unit, $size);
    }
    
    function setSourceFile($filename)
    {
        // For this simplified version, we'll just store the filename
        // In a real implementation, this would parse the PDF
        $this->currentTemplate = $filename;
        return 1; // Return page count (simplified)
    }
    
    function importPage($pageno, $box = '/CropBox')
    {
        // For this simplified version, we'll just return a template ID
        $templateId = 'template_' . $pageno;
        $this->templates[$templateId] = array(
            'file' => $this->currentTemplate,
            'page' => $pageno,
            'box' => $box
        );
        return $templateId;
    }
    
    function useTemplate($templateId, $x = null, $y = null, $w = 0, $h = 0)
    {
        // For this simplified version, we'll just add a comment
        // In a real implementation, this would overlay the template
        if ($x === null) $x = 0;
        if ($y === null) $y = 0;
        
        $this->_out('% Template overlay: ' . $templateId);
        // Add a rectangle to represent where the template would be
        if ($w > 0 && $h > 0) {
            $this->Rect($x, $y, $w, $h);
        }
    }
    
    function Rect($x, $y, $w, $h, $style='')
    {
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
    }
}
?>