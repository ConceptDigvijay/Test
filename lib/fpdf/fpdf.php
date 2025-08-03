<?php
/**
 * FPDF - Simple PDF generation class
 * Simplified version for the Ganpati donation project
 */

class FPDF
{
    protected $page;
    protected $n;
    protected $buffer;
    protected $pages;
    protected $state;
    protected $compress;
    protected $k;
    protected $DefOrientation;
    protected $CurOrientation;
    protected $StdPageSizes;
    protected $DefPageSize;
    protected $CurPageSize;
    protected $PageSizes;
    protected $wPt, $hPt;
    protected $w, $h;
    protected $lMargin;
    protected $tMargin;
    protected $rMargin;
    protected $bMargin;
    protected $cMargin;
    protected $x, $y;
    protected $lasth;
    protected $LineWidth;
    protected $FontFamily;
    protected $FontStyle;
    protected $underline;
    protected $CurrentFont;
    protected $FontSizePt;
    protected $FontSize;
    protected $DrawColor;
    protected $FillColor;
    protected $TextColor;
    protected $ColorFlag;
    protected $AutoPageBreak;
    protected $PageBreakTrigger;
    protected $InHeader;
    protected $InFooter;
    protected $ZoomMode;
    protected $LayoutMode;
    protected $PDFVersion;
    protected $ws;
    protected $offsets;

    function __construct($orientation='P', $unit='mm', $size='A4')
    {
        // Initialize properties
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = array();
        $this->PageSizes = array();
        $this->state = 0;
        $this->compress = true;
        $this->k = 1;
        $this->DefOrientation = $orientation;
        $this->CurOrientation = $orientation;
        $this->ws = 0;
        $this->offsets = array();
        
        // Page sizes
        $this->StdPageSizes = array('a3'=>array(841.89,1190.55), 'a4'=>array(595.28,841.89), 'a5'=>array(420.94,595.28),
                                   'letter'=>array(612,792), 'legal'=>array(612,1008));
        
        if(is_string($size))
            $size = isset($this->StdPageSizes[strtolower($size)]) ? $this->StdPageSizes[strtolower($size)] : $this->StdPageSizes['a4'];
        $this->DefPageSize = $size;
        $this->CurPageSize = $size;
        
        // Set scale factor
        if($unit=='pt')
            $this->k = 1;
        elseif($unit=='mm')
            $this->k = 72/25.4;
        elseif($unit=='cm')
            $this->k = 72/2.54;
        elseif($unit=='in')
            $this->k = 72;
        else
            $this->Error('Incorrect unit: '.$unit);
        
        // Page dimensions
        $this->wPt = $this->DefPageSize[0];
        $this->hPt = $this->DefPageSize[1];
        $this->w = $this->wPt/$this->k;
        $this->h = $this->hPt/$this->k;
        
        // Page margins (1 cm)
        $margin = 28.35/$this->k;
        $this->SetMargins($margin,$margin);
        $this->cMargin = $margin/10;
        $this->SetAutoPageBreak(true,2*$margin);
        
        // Line width (0.2 mm)
        $this->LineWidth = .567/$this->k;
        
        // Default display mode
        $this->SetDisplayMode('default');
        
        // Enable compression
        $this->SetCompression(true);
        
        // Set default PDF version number
        $this->PDFVersion = '1.3';
        
        // Initialize current font
        $this->CurrentFont = array('i' => 1, 'cw' => array_fill(0, 256, 600));
        
        // Initialize colors
        $this->DrawColor = '0 G';
        $this->FillColor = '0 g';
        $this->TextColor = '0 g';
        $this->ColorFlag = false;
    }
    
    function SetMargins($left, $top, $right=null)
    {
        $this->lMargin = $left;
        $this->tMargin = $top;
        if($right===null)
            $right = $left;
        $this->rMargin = $right;
    }
    
    function SetAutoPageBreak($auto, $margin=0)
    {
        $this->AutoPageBreak = $auto;
        $this->bMargin = $margin;
        $this->PageBreakTrigger = $this->h-$margin;
    }
    
    function SetDisplayMode($zoom, $layout='default')
    {
        $this->ZoomMode = $zoom;
        $this->LayoutMode = $layout;
    }
    
    function SetCompression($compress)
    {
        if(function_exists('gzcompress'))
            $this->compress = $compress;
        else
            $this->compress = false;
    }
    
    function AddPage($orientation='', $size='')
    {
        $family = $this->FontFamily;
        $style = $this->FontStyle.($this->underline ? 'U' : '');
        $fontsize = $this->FontSizePt;
        $lw = $this->LineWidth;
        $dc = $this->DrawColor;
        $fc = $this->FillColor;
        $tc = $this->TextColor;
        $cf = $this->ColorFlag;
        if($this->page>0)
        {
            $this->InFooter = true;
            $this->Footer();
            $this->InFooter = false;
            $this->_endpage();
        }
        $this->_beginpage($orientation,$size);
        $this->_out('2 J');
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w',$lw*$this->k));
        if($family)
            $this->SetFont($family,$style,$fontsize);
        $this->DrawColor = $dc;
        if($dc!='0 G')
            $this->_out($dc);
        $this->FillColor = $fc;
        if($fc!='0 g')
            $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
        $this->InHeader = true;
        $this->Header();
        $this->InHeader = false;
        if($this->LineWidth!=$lw)
        {
            $this->LineWidth = $lw;
            $this->_out(sprintf('%.2F w',$lw*$this->k));
        }
        if($family)
            $this->SetFont($family,$style,$fontsize);
        if($this->DrawColor!=$dc)
        {
            $this->DrawColor = $dc;
            $this->_out($dc);
        }
        if($this->FillColor!=$fc)
        {
            $this->FillColor = $fc;
            $this->_out($fc);
        }
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
    }
    
    function Header()
    {
        // To be implemented in derived class
    }
    
    function Footer()
    {
        // To be implemented in derived class
    }
    
    function SetFont($family, $style='', $size=0)
    {
        if($family=='')
            $family = $this->FontFamily;
        else
            $family = strtolower($family);
        $style = strtoupper($style);
        if(strpos($style,'U')!==false)
        {
            $this->underline = true;
            $style = str_replace('U','',$style);
        }
        else
            $this->underline = false;
        if($style=='IB')
            $style = 'BI';
        if($size==0)
            $size = $this->FontSizePt;
        
        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSizePt = $size;
        $this->FontSize = $size/$this->k;
        if($this->page>0)
            $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
    }
    
    function SetTextColor($r, $g=null, $b=null)
    {
        if(($r==0 && $g==0 && $b==0) || $g===null)
            $this->TextColor = sprintf('%.3F g',$r/255);
        else
            $this->TextColor = sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
        $this->ColorFlag = ($this->FillColor!=$this->TextColor);
    }
    
    function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $k = $this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
        {
            $x = $this->x;
            $ws = $this->ws;
            if($ws>0)
            {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->AddPage($this->CurOrientation,$this->CurPageSize);
            $this->x = $x;
            if($ws>0)
            {
                $this->ws = $ws;
                $this->_out(sprintf('%.3F Tw',$ws*$k));
            }
        }
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $s = '';
        if($fill || $border==1)
        {
            if($fill)
                $op = ($border==1) ? 'B' : 'f';
            else
                $op = 'S';
            $s = sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        if(is_string($border))
        {
            $x = $this->x;
            $y = $this->y;
            if(strpos($border,'L')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'T')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
            if(strpos($border,'R')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'B')!==false)
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        }
        if($txt!=='')
        {
            if($align=='R')
                $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C')
                $dx = ($w-$this->GetStringWidth($txt))/2;
            else
                $dx = $this->cMargin;
            if($this->ColorFlag)
                $s .= 'q '.$this->TextColor.' ';
            $txt2 = str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt2);
            if($this->underline)
                $s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
            if($this->ColorFlag)
                $s .= ' Q';
            if($link)
                $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$this->GetStringWidth($txt),$this->FontSize,$link);
        }
        if($s)
            $this->_out($s);
        $this->lasth = $h;
        if($ln>0)
        {
            $this->y += $h;
            if($ln==1)
                $this->x = $this->lMargin;
        }
        else
            $this->x += $w;
    }
    
    function GetStringWidth($s)
    {
        $s = (string)$s;
        if(!isset($this->CurrentFont['cw']))
            return strlen($s) * 0.6; // Approximate width
        $cw = &$this->CurrentFont['cw'];
        $w = 0;
        $l = strlen($s);
        for($i=0;$i<$l;$i++) {
            $char = ord($s[$i]);
            if(isset($cw[$char]))
                $w += $cw[$char];
            else
                $w += 600; // Default width
        }
        return $w*$this->FontSize/1000;
    }
    
    function Ln($h=null)
    {
        $this->x = $this->lMargin;
        if($h===null)
            $this->y += $this->lasth;
        else
            $this->y += $h;
    }
    
    function SetY($y)
    {
        $this->x = $this->lMargin;
        if($y>=0)
            $this->y = $y;
        else
            $this->y = $this->h+$y;
    }
    
    function SetX($x)
    {
        if($x>=0)
            $this->x = $x;
        else
            $this->x = $this->w+$x;
    }
    
    function SetXY($x, $y)
    {
        $this->SetY($y);
        $this->SetX($x);
    }
    
    function Output($dest='', $name='', $isUTF8=false)
    {
        if($this->state<3)
            $this->Close();
        
        if($dest=='')
        {
            if($name=='')
            {
                $name = 'doc.pdf';
                $dest = 'I';
            }
            else
                $dest = 'F';
        }
        switch(strtoupper($dest))
        {
            case 'I':
                $this->_checkoutput();
                if(PHP_SAPI!='cli')
                {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="'.$name.'"');
                    header('Cache-Control: private, max-age=0, must-revalidate');
                    header('Pragma: public');
                }
                echo $this->buffer;
                break;
            case 'D':
                $this->_checkoutput();
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="'.$name.'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $this->buffer;
                break;
            case 'F':
                $f = fopen($name,'wb');
                if(!$f)
                    $this->Error('Unable to create output file: '.$name);
                fwrite($f,$this->buffer,strlen($this->buffer));
                fclose($f);
                break;
            case 'S':
                return $this->buffer;
            default:
                $this->Error('Incorrect output destination: '.$dest);
        }
        return '';
    }
    
    function Close()
    {
        if($this->state==3)
            return;
        if($this->page==0)
            $this->AddPage();
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;
        $this->_endpage();
        $this->_enddoc();
    }
    
    // Protected methods
    protected function _beginpage($orientation, $size)
    {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->state = 2;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
        $this->FontFamily = '';
        if(!$orientation)
            $orientation = $this->DefOrientation;
        else
        {
            $orientation = strtoupper($orientation);
        }
        if(!$size)
            $size = $this->DefPageSize;
        else
        {
            if(is_string($size))
                $size = isset($this->StdPageSizes[strtolower($size)]) ? $this->StdPageSizes[strtolower($size)] : $this->StdPageSizes['a4'];
        }
        if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1])
        {
            if($orientation=='P')
            {
                $this->w = $size[0];
                $this->h = $size[1];
            }
            else
            {
                $this->w = $size[1];
                $this->h = $size[0];
            }
            $this->wPt = $this->w*$this->k;
            $this->hPt = $this->h*$this->k;
            $this->PageBreakTrigger = $this->h-$this->bMargin;
            $this->CurOrientation = $orientation;
            $this->CurPageSize = $size;
        }
        if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
            $this->PageSizes[$this->page] = array($this->wPt, $this->hPt);
    }
    
    protected function _endpage()
    {
        $this->state = 1;
    }
    
    protected function _enddoc()
    {
        $this->state = 3;
        $this->_putpages();
        $this->_putresources();
        $this->_putinfo();
        $this->_putcatalog();
        $this->_puttrailer();
        $this->_putheader();
        $this->buffer = '%PDF-'.$this->PDFVersion."\n".$this->buffer;
    }
    
    protected function _putpages()
    {
        $nb = $this->page;
        for($n=1;$n<=$nb;$n++)
            if(!isset($this->PageSizes[$n]))
                $this->PageSizes[$n] = array($this->wPt, $this->hPt);
        
        for($n=1;$n<=$nb;$n++)
        {
            $this->_newobj();
            $this->_out('<</Type /Page');
            $this->_out('/Parent 1 0 R');
            if(isset($this->PageSizes[$n]))
                $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]',$this->PageSizes[$n][0],$this->PageSizes[$n][1]));
            $this->_out('/Resources 2 0 R');
            $this->_out('/Contents '.($this->n+1).' 0 R>>');
            $this->_out('endobj');
            
            $p = isset($this->pages[$n]) ? $this->pages[$n] : '';
            $this->_newobj();
            $this->_out('<</Length '.strlen($p).'>>');
            $this->_putstream($p);
            $this->_out('endobj');
        }
        
        $this->_newobj(1);
        $this->_out('<</Type /Pages');
        $kids = '/Kids [';
        for($i=0;$i<$nb;$i++)
            $kids .= (3+2*$i).' 0 R ';
        $kids .= ']';
        $this->_out($kids);
        $this->_out('/Count '.$nb);
        $this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]',$this->wPt,$this->hPt));
        $this->_out('>>');
        $this->_out('endobj');
    }
    
    protected function _putresources()
    {
        $this->_newobj(2);
        $this->_out('<</ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->_out('/Font <<');
        $this->_out('/F1 <</Type /Font /Subtype /Type1 /BaseFont /Helvetica>>');
        $this->_out('>>');
        $this->_out('>>');
        $this->_out('endobj');
    }
    
    protected function _putinfo()
    {
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/Producer '.$this->_textstring('FPDF Simple'));
        $this->_out('/CreationDate '.$this->_textstring('D:'.@date('YmdHis')));
        $this->_out('>>');
        $this->_out('endobj');
    }
    
    protected function _putcatalog()
    {
        $this->_newobj();
        $this->_out('<<');
        $this->_out('/Type /Catalog');
        $this->_out('/Pages 1 0 R');
        $this->_out('>>');
        $this->_out('endobj');
    }
    
    protected function _puttrailer()
    {
        $this->_out('xref');
        $this->_out('0 '.($this->n+1));
        $this->_out('0000000000 65535 f ');
        for($i=1;$i<=$this->n;$i++)
            $this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
        $this->_out('trailer');
        $this->_out('<<');
        $this->_out('/Size '.($this->n+1));
        $this->_out('/Root '.$this->n.' 0 R');
        $this->_out('/Info '.($this->n-1).' 0 R');
        $this->_out('>>');
        $this->_out('startxref');
        $this->_out($this->offsets[$this->n]);
        $this->_out('%%EOF');
    }
    
    protected function _putheader()
    {
        // PDF header is added in _enddoc()
    }
    
    protected function _newobj($objno=null)
    {
        if($objno===null)
            $objno = ++$this->n;
        if(!isset($this->offsets))
            $this->offsets = array();
        $this->offsets[$objno] = strlen($this->buffer);
        $this->_out($objno.' 0 obj');
        return $objno;
    }
    
    protected function _out($s)
    {
        if($this->state==2)
            $this->pages[$this->page] .= $s."\n";
        else
            $this->buffer .= $s."\n";
    }
    
    protected function _putstream($s)
    {
        $this->_out('stream');
        $this->_out($s);
        $this->_out('endstream');
    }
    
    protected function _textstring($s)
    {
        return '('.$s.')';
    }
    
    protected function _checkoutput()
    {
        if(PHP_SAPI!='cli')
        {
            if(headers_sent($file,$line))
                $this->Error("Some data has already been output, can't send PDF file (output started at $file:$line)");
        }
        if(ob_get_length())
        {
            if(preg_match('/^(\xEF\xBB\xBF)?\s*$/',ob_get_contents()))
            {
                ob_clean();
            }
            else
                $this->Error("Some data has already been output, can't send PDF file");
        }
    }
    
    protected function AcceptPageBreak()
    {
        return $this->AutoPageBreak;
    }
    
    protected function _dounderline($x, $y, $txt)
    {
        $w = $this->GetStringWidth($txt)+$this->ws*substr_count($txt,' ');
        return sprintf('%.2F %.2F %.2F %.2F re f',$x*$this->k,($this->h-($y-2))*$this->k,$w*$this->k,-1);
    }
    
    function Error($msg)
    {
        throw new Exception('FPDF error: '.$msg);
    }
}
?>