<?php

if (count(debug_backtrace()) == 0) {
  header('Content-Type: text/plain; charset=UTF-8', TRUE, 403);
  die("The page cannot be displayed.\r\nThe request has not been fulfilled because the server does not authorise access to this request externally.");
}

class Paging {
  private $_style = Paging_Style;
  private $_urlBuilder = Url_Builder;

  // what page are we on and how many pages should we show?
  private $_resultsPerPage = 0;
  private $_results = 0;
  private $_offset = 0;
  private $_page = 1;
  private $_pageOffset = 1;
  private $_numPages = 1;
  private $_pagesToShow = 10;

  // is the paging enabled?
  private $_enabled = TRUE;

  // what to show
  private $_showJumpToFirstLink = TRUE;
  private $_showJumpToPreviousLink = TRUE;
  private $_showJumpToNextLink = TRUE;
  private $_showJumpToLastLink = TRUE;

  // formats
  private $_seperatorFormat = '<br />';
  private $_informationFormat = 'Showing page %d of %d';
  private $_pagesFormat = 'Page: %s';
  private $_linkFormat = '<a href="%s">%s</a>';
  private $_linkSeperatorFormat = ' ';
  private $_jumpToFirstFormat = '&lt;&lt;';
  private $_jumpToPreviousFormat = '&lt;';
  private $_jumpToNextFormat = '&gt;';
  private $_jumpToLastFormat = '&gt;&gt;';
  private $_currentPageFormat = '<strong>%d</strong>';

  public function __construct () {
    $request = new Request;

    $this->_urlBuilder = new Url_Builder;
    $this->_results = Setting::MAX_SQL_RESULTS;
    $this->_style = Paging_Style::normal;

    if (is_numeric($request->queryString('page')) && ceil($request->queryString('page')) > 0) {
      $this->page = intval($request->queryString('page'));
    }

    if (is_numeric($request->queryString('results')) && ceil($request->queryString('results')) > 0 && $request->queryString('results') < $this->_results) {
      $this->_resultsPerPage = intval($request->queryString('results'));
    }
    else {
      $this->_resultsPerPage = Setting::PAGING_RESULTS_PER_PAGE;
    }
  }

  public function __set ($variableName, $value) {
    switch ($variableName) {
      // how many results should show per page?
      case 'resultsPerPage':
        if (Data_Type::isInt($value) == FALSE) {
          throw new Data_Type_Exception (Data_Type::Int, $variableName, $value);
          break;
        }

        $value = ceil($value);

        if ($value < 1 || $value > Setting::MAX_SQL_RESULTS) {
          $this->_resultsPerPage = Setting::MAX_SQL_RESULTS;
        }
        else {
          $this->_resultsPerPage = $value;
        }
      break;

      // how many actual results are there?
      case 'results':
        if (Data_Type::isInt($value) == FALSE) {
          throw new Data_Type_Exception (Data_Type::Int, $variableName, $value);
          break;
        }

        $value = ceil($value);

        if ($value < 0) {
          $value = 0;
        }
        elseif ($value > Setting::MAX_SQL_RESULTS) {
          $this->_results = Setting::MAX_SQL_RESULTS;
        }
        else {
          $this->_results = $value;
        }
      break;

      // the page for the results
      case 'page':
        if (Data_Type::isInt($value) == FALSE) {
          throw new Data_Type_Exception (Data_Type::Int, $variableName, $value);
          break;
        }

        $value = ceil($value);

        if ($value < 2) {
          $this->_offset = 0;
          $this->_page = 1;
        }
        elseif ($value > Setting::MAX_SQL_RESULTS) {
          $this->_offset = Setting::MAX_SQL_RESULTS;
          $this->_page = Setting::MAX_SQL_RESULTS + 1;
        }
        else {
          $this->_offset = $value - 1;
          $this->_page = $value;
        }
      break;

      case 'enabled':
        if (Data_Type::isBool($value) == TRUE) {
          $this->{'_' . $variableName} = $value;
          break;
        }
        else {
          throw new Data_Type_Exception (Data_Type::Bool, $variableName, $value);
        }
      break;

      case 'offset':
        throw new Missing_Value_Exception ($variableName);
      break;

      default:
        throw new Missing_Value_Exception ($variableName);
    }
  }

  public function __get ($variableName) {
    switch ($variableName) {
      case 'resultsPerPage':
      case 'results':
      case 'page':
      case 'enabled':
        return $this->{'_' . $variableName};
      break;

      case 'offset':
        $this->_offset = $this->_resultsPerPage * ($this->_page - 1);
        return $this->_offset;
      break;

      case 'pages':
      case 'information':
        return $this->$variableName();
      break;

      default:
        throw new Missing_Value_Exception ($variableName);
    }
  }

  public function  __toString() {
    if ($this->_enabled == TRUE) {
      try {
        $this->_build();
        return $this->information(FALSE) . $this->_seperatorFormat . $this->pages(FALSE);
      }
      catch (Exception $ex) {
        echo $ex->getMessage();
        exit;
      }
    }
    else {
      return '';
    }
  }

  protected function _build () {
    // if there are no results don't show the pagination
    if ($this->_results < 1) {
      $this->_enabled = FALSE;
      return;
    }

    $this->_numPages = ceil($this->_results / $this->_resultsPerPage);

    switch ($this->_style) {
      case Paging_Style::classic:
      case Paging_Style::digg:
      case Paging_Style::punBB:
        throw new Exception('not supported yet');
      break;

      case Paging_Style::normal:
      default:
        // make sure this is an odd number
        if ($this->_pagesToShow % 2 == 0) {
          --$this->_pagesToShow;
        }

        $this->_pageOffset = $this->_page - floor($this->_pagesToShow / 2);

        // make sure we don't show pages that don't exist
        if ($this->_numPages < $this->_pagesToShow) {
          $this->_pagesToShow = $this->_numPages;
        }

        // make sure we don't show negative pages
        if ($this->_pageOffset < 1) {
          $this->_pageOffset = 1;
        }
        // make sure we don't show more pages than we should
        elseif ($this->page + floor($this->_pagesToShow / 2) >= $this->_numPages) {
          $this->_pageOffset = $this->_numPages - $this->_pagesToShow + 1;
        }

        // hide first and previous links
        if ($this->_page <= 1) {
          $this->_showJumpToFirstLink = FALSE;
          $this->_showJumpToPreviousLink = FALSE;
        }

        // hide next and last links
        if ($this->_page >= $this->_numPages) {
          $this->_showJumpToNextLink = FALSE;
          $this->_showJumpToLastLink = FALSE;
        }
      break;
    }
  }

  public function information ($build = TRUE) {
    $information = '';

    if ($this->_enabled == TRUE) {
      if ($build == TRUE) {
        $this->_build();
      }
      $information = sprintf($this->_informationFormat, $this->_page, $this->_numPages);
    }

    return $information;
  }

  public function pages ($build = TRUE) {
    $pages = '';
    $links = '';
    $pageNumber = 0;

    if ($this->_enabled == TRUE) {
      if ($build == TRUE) {
        $this->_build();
      }

      if ($this->_showJumpToFirstLink == TRUE) {
        $this->_urlBuilder->addQueryStringArg('page', 1);
        $links .= sprintf($this->_linkFormat, $this->_urlBuilder->url, $this->_jumpToFirstFormat) . $this->_linkSeperatorFormat;
      }

      if ($this->_showJumpToPreviousLink == TRUE) {
        $this->_urlBuilder->addQueryStringArg('page', $this->_page - 1);
        $links .= sprintf($this->_linkFormat, $this->_urlBuilder->url, $this->_jumpToPreviousFormat) . $this->_linkSeperatorFormat;
      }

      for ($i = 0, $x = $this->_pageOffset; $i < $this->_pagesToShow; ++$i, ++$x) {
        if ($this->_page != $x) {
          $this->_urlBuilder->addQueryStringArg('page', $x);
          $links .= sprintf($this->_linkFormat, $this->_urlBuilder->url, $x) . $this->_linkSeperatorFormat;
        }
        else {
          $links .= sprintf($this->_currentPageFormat, $x) . $this->_linkSeperatorFormat;
        }
      }

      if ($this->_showJumpToNextLink == TRUE) {
        $this->_urlBuilder->addQueryStringArg('page', $this->_page + 1);
        $links .= sprintf($this->_linkFormat, $this->_urlBuilder->url, $this->_jumpToNextFormat);

        if ($this->_showJumpToLastLink == TRUE) {
          $links .= $this->_linkSeperatorFormat;
        }
      }

      if ($this->_showJumpToLastLink == TRUE) {
        $this->_urlBuilder->addQueryStringArg('page', floor($this->_resultsPerPage / $this->_results));
        $links .= sprintf($this->_linkFormat, $this->_urlBuilder->url, $this->_jumpToLastFormat);
      }

      // remove the last link seperator if not needed
      if ($this->_showJumpToNextLink == FALSE && $this->_showJumpToLastLink == FALSE) {
        $links = substr($links, 0, strlen($links) - strlen($this->_linkSeperatorFormat));
      }

      $pages = sprintf($this->_pagesFormat, $links);
    }

    return $pages;
  }
}

define('Paging_Style', 'Paging_Style');
final class Paging_Style extends Enum {
  const normal = 0x00000001;
  const classic = 0x00000010;
  const digg = 0x00000100;
  const punBB = 0x00001000;
}

?>