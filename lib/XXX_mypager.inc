<?php
/******************************************************/
/*Company:                                            */
/*Author :Ken Tsai                                    */
/*Date   : from 2002.08.13                                    */
/*Description: mypager類別程式, 繼承ADODB的pager類別  */
/******************************************************/

// You have to include adodb driver before.
include_once("./adodb5/adodb-pager.inc.php");
include_once("./adodb5/tohtml.inc.php");

class MyPager extends ADODB_Pager
{
	var $op;
	var $funcNames;
	var $funcUrls;
	var $functions;

	function MyPager(&$db,$sql,$id = 'adodb',$showPageLinks = false)
	{
		$this->ADODB_Pager($db,$sql,$id,$showPageLinks);
		$this->page = _PAGE_COUNT;
		$this->first = _FIRST_PAGE;
		$this->last = _LAST_PAGE;
		$this->prev = _PREV_PAGE;
		$this->next = _NEXT_PAGE;
		$this->functions = _FUNCTIONS;
		$this->op = null;
		$this->funcNames = array();
		$this->funcUrls = array();
	}

	function setFunctions($funcNames,$funcUrls)
	{
		if(is_array($funcNames) and is_array($funcUrls)) {
			$this->funcNames = array_values($funcNames);
			$this->funcUrls = array_values($funcUrls);
		}
	}

	function setRenderGridLayout($attributes="width=100% border=1 bgcolor=white",$gridHeader=false)
	{
		if(!empty($attributes)) $this->gridAttributes = $attributes;
		if(!empty($gridHeader)) $this->gridHeader = $gridHeader;
	}

	function setOp($op)
	{
		if(!empty($op)) $this->op = "op=".$op;
	}

	function Render_First($anchor=true)
	{
		if ($anchor) {
			$html = "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?";
			if(!empty($this->op)) $html .= "$this->op&";
			$html .= $this->id."_next_page=1\">$this->first</a>&nbsp;";
		} else {
			$html = "&nbsp;".$this->first."&nbsp;";
		}
		echo $html;
	}

	function Render_Function($rows=10)
	{
		$this->Render($rows);
	}

	function Render_Next($anchor=true)
	{
		if ($anchor) {
			$nextPage = $this->rs->AbsolutePage() + 1;
			$html = "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?";
			if(!empty($this->op)) $html .= "$this->op&";
			$html .= $this->id."_next_page=".$nextPage."\">$this->next</a>&nbsp;";
		} else {
			$html =  "&nbsp;".$this->next."&nbsp;";
		}
		echo $html;
	}

	function Render_Last($anchor=true)
	{
		if (!$this->db->pageExecuteCountRows) return;
		if ($anchor) {
			$html = "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?";
			if(!empty($this->op)) $html .= "$this->op&";
			$html .= $this->id."_next_page=".$this->rs->LastPageNo()."\">$this->last</a>&nbsp;";
		} else {
			$html = "&nbsp;".$this->last."&nbsp;";
		}
		echo $html;
	}

	function Render_PageLinks()
	{
		$pages = $this->rs->LastPageNo();
		$linksperpage = $this->linksPerPage ? $this->linksPerPage : $pages;
		for($i=1; $i <= $pages; $i+=$linksperpage)
		{
			if($this->rs->AbsolutePage() >= $i)
			{
				$start = $i;
			}
		}
		$numbers = '';
		$end = $start+$linksperpage-1;
		$link = $this->id . "_next_page";
		if($end > $pages) $end = $pages;

		if ($this->startLinks && $start > 1) {
			$pos = $start - 1;
			$numbers .= "<a href=".$_SERVER['PHP_SELF']."?";
			if(!empty($this->op)) $numbers .= "$this->op&";
			$numbers .= "$link=$pos>$this->startLinks</a>&nbsp;";
		}

		for($i=$start; $i <= $end; $i++) {
			if ($this->rs->AbsolutePage() == $i) {
				$numbers .= "<font color=$this->linkSelectedColor><b>$i</b></font>&nbsp;";
			} else {
				$numbers .= "<a href=".$_SERVER['PHP_SELF']."?";
				if(!empty($this->op)) $numbers .= "$this->op&";
				$numbers .= "$link=$i>$i</a>&nbsp;";
			}
		}
		if ($this->moreLinks && $end < $pages) {
			$numbers .= "<a href=".$_SERVER['PHP_SELF']."?";
			if(!empty($this->op)) $numbers .= "$this->op&";
			$numbers .= "$link=$i>$this->moreLinks</a>&nbsp;";
		}
		echo $numbers;
	}

	function Render_Prev($anchor=true)
	{
		if ($anchor) {
			$prevPage = $this->rs->AbsolutePage() - 1;
			$html = "&nbsp;<a href=\"".$_SERVER['PHP_SELF']."?";
			if(!empty($this->op)) $html .= "$this->op&";
			$html .= $this->id."_next_page=".$prevPage."\">$this->prev</a>&nbsp;";
		} else {
			$html = "&nbsp;".$this->prev."&nbsp;";
		}
		echo $html;
	}

	function RenderGrid()
	{
		global $gSQLBlockRows; // used by rs2html to indicate how many rows to display

		ob_start();
		$gSQLBlockRows = $this->rows;
		$this->rs2html($this->rs,$this->gridAttributes,$this->gridHeader,$this->htmlSpecialChars,true);
		$s = ob_get_contents();
		ob_end_clean();
		return $s;
	}

	function RenderLayout($header,$grid,$footer,$attributes='border=0 bgcolor=white')
	{
		echo "<center>\r\n<table ".$attributes.">\r\n".
		"<tr>\r\n<td align=\"center\">".$header."</td>\r\n</tr>\r\n".
		"<tr>\r\n<td>".$grid."</td>\r\n</tr>\r\n".
		"<tr>\r\n<td align=\"center\">".$footer."</td>\r\n</tr>\r\n</table>\r\n</center>\r\n";
	}

	function rs2html(&$rs,$ztabhtml=false,$zheaderarray=false,$htmlspecialchars=true,$echo = true)
	{
		$s ='';$rows=0;$docnt = false;
		GLOBAL $gSQLMaxRows,$gSQLBlockRows;

		if (!$rs) {
			printf(ADODB_BAD_RS,'rs2html');
			return false;
		}

		// modified by Ching-Yuan Lai, 02/24/2004
		// modify begin
		$rschk = $this->checkRsEmpty($rs);
		// modify end

		if (! $ztabhtml) $ztabhtml = "BORDER='1' WIDTH='98%'";
		//else $docnt = true;
		$typearr = array();
		$ncols = $rs->FieldCount();
		$hdr = "<TABLE COLS=$ncols $ztabhtml><tr>\n\n";
		for ($i=0; $i < $ncols; $i++) {
			$field = $rs->FetchField($i);
			if ($zheaderarray) $fname = $zheaderarray[$i];
			else $fname = htmlspecialchars($field->name);
			$typearr[$i] = $rs->MetaType($field->type,$field->max_length);
			//print " $field->name $field->type $typearr[$i] ";

			if (strlen($fname)==0) $fname = '&nbsp;';
			$hdr .= "<TH>$fname</TH>";
		}

		// modified by Ching-Yuan Lai, 02/24/2004
		// modify begin
		$hdr .= $this->addFunctionsTitle();
		// modify end

		$hdr .= "\n</tr>";

		if ($echo && $rschk) print $hdr."\n\n";
		else $html = $hdr;

		// modify end

		// smart algorithm - handles ADODB_FETCH_MODE's correctly!
		$numoffset = isset($rs->fields[0]);

		while (!$rs->EOF) {

			$s .= "<TR valign=top>\n";

			for ($i=0; $i < $ncols; $i++) {
				if ($i===0) $v=($numoffset) ? $rs->fields[0] : reset($rs->fields);
				else $v = ($numoffset) ? $rs->fields[$i] : next($rs->fields);

				$type = $typearr[$i];
				switch($type) {
					case 'T':
					$s .= "	<TD>".$rs->UserTimeStamp($v,"D d, M Y, h:i:s") ."&nbsp;</TD>\n";
					break;
					case 'D':
					$s .= "	<TD>".$rs->UserDate($v,"D d, M Y") ."&nbsp;</TD>\n";
					break;
					case 'I':
					case 'N':
					// modified by Ching-Yuan Lai, 2005/01/26
					$s .= "	<TD align=right>".stripslashes((trim($v))) ."&nbsp;</TD>\n";
					//$s .= "	<TD align=right>".(trim($v))."&nbsp;</TD>\n";

					break;
					default:
					if ($htmlspecialchars) $v = htmlspecialchars(trim($v));
					//modified by Bruce Huang, 2005/05/11
					//$v = trim($v);
					$v =html_entity_decode(trim($v));
					if (strlen($v) == 0) $v = '&nbsp;';
					//$s .= "	<TD>".str_replace("\n",'<br>',$v)."</TD>\n";
					// modified by Ching-Yuan Lai, 2005/01/26
					$s .= "	<TD>". str_replace("\n",'<br>',stripslashes($v)) ."</TD>\n";

				}
			} // for

			// modified by Ching-Yuan Lai, 02/24/2004
			// modify begin
			$s .= $this->addFunctions($rs->fields[0]);
			// modify end

			$s .= "</TR>\n\n";

			$rows += 1;
			if ($rows >= $gSQLMaxRows) {
				$rows = "<p>Truncated at $gSQLMaxRows</p>";
				break;
			} // switch

			$rs->MoveNext();

			// additional EOF check to prevent a widow header
			if (!$rs->EOF && $rows % $gSQLBlockRows == 0) {

				//if (connection_aborted()) break;// not needed as PHP aborts script, unlike ASP
				if ($echo && $rschk) print $s . "</TABLE>\n\n";
				else $html .= $s ."</TABLE>\n\n";
				$s = $hdr;
			}
		} // while

		if ($echo && $rschk) print $s."</TABLE>\n\n";
		else $html .= $s."</TABLE>\n\n";

		if ($docnt) if ($echo) print "<H2>".$rows." Rows</H2>";

		return ($echo && $rschk) ? $rows : $html;
	}

	// modified by Ching-Yuan Lai, 07/27/2004
	function checkRsEmpty(&$rs)
	{
		if ($rs == FALSE || $rs->EOF || $rs->RecordCount() == 0) {
			print "<br><center><h3>"._NO_RECORD."</h3></center><br>";
			return false;
		} else {
			return true;
		}
	}

	// modified by Ching-Yuan Lai, 02/24/2004
	function addFunctionsTitle()
	{
		$str = "";
		if (!empty($this->funcUrls) && !empty($this->funcNames)) {
			$str = "<TH>".$this->functions."</TH>\r\n";
		}
		return $str;
	}

	// modified by Ching-Yuan Lai, 02/24/2004
	function addFunctions($primaryKeyValue)
	{
		$str = "";
		if (!empty($this->funcUrls) && !empty($this->funcNames)) {
			$count = count($this->funcUrls);
			for($i = 0; $i < $count; $i++) {
				$url = $this->funcUrls[$i]."&pkid=".$primaryKeyValue;
				$func .= "<a href=\"".$url."\">".$this->funcNames[$i]."</a>\r\n";
			}
			$str = "<TD>".$func."</TD>\r\n";
		}
		return $str;
	}
}
?>
