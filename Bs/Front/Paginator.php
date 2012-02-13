<?php

class Bs_Front_Paginator
{
    protected $_query;
    protected $_maxPerPage;
    protected $_currentPage;
    protected $_totalPages;
    protected $_carouselItems = 5;

    public function __construct(Doctrine_Query $q, $maxPerPage = 10)
    {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $this->_query = $q;
        $this->_maxPerPage = $maxPerPage;
        $this->_currentPage = isset($request->p) ? $request->p : 1;
        $this->_totalItems =  $this->_query->count();
        $this->_totalPages =  ceil($this->_totalItems / $this->_maxPerPage);

        if($request->p > $this->_totalPages)
        {
            $this->_currentPage = $this->_totalPages;
        }
    }

    public function getData()
    {
        return $this->_query->limit($this->_maxPerPage)
                            ->offset(($this->_currentPage -1)*$this->_maxPerPage)
                            ->execute();
    }

    public function render($view)
    {
        $firstLink = $view->url(array('p'=>"1"), 'default', false);
        $first = $this->_currentPage > 1 ? "<a href=\"{$firstLink}\">Primera</a>" : "Primera";

        $prevLink = $view->url(array('p'=>$this->_currentPage-1), 'default', false);
        $prev = $this->_currentPage > 1 ? "<a href=\"{$prevLink}\">Anterior</a>" : "Anterior";

        $carousel = $this->renderCarouse($view);

        $nextLink = $view->url(array('p'=>$this->_currentPage+1), 'default', false);
        $next = $this->_currentPage < $this->_totalPages ? "<a href=\"{$nextLink}\">Siguiente</a>" : "Siguiente";

        $lastLink = $view->url(array('p'=>$this->_totalPages), 'default', false);
        $last = $this->_currentPage < $this->_totalPages ? "<a href=\"{$lastLink}\">&Uacute;ltima</a>" : "&Uacute;ltima";

        return "{$first} {$prev} {$carousel} {$next} {$last}";
    }

    public function renderCarouse($view)
    {
        $body = "";
        $start = 1;

        if($this->_currentPage > floor($this->_carouselItems / 2))
        {
            $start = $this->_currentPage - floor($this->_carouselItems / 2);
        }

        if( $this->_totalPages < ($this->_currentPage + floor($this->_carouselItems / 2)))
        {
            $start -= (floor($this->_carouselItems / 2)- ($this->_totalPages - $this->_currentPage));
        }

        if($start < 1)
        {
            $start = 1;
        }

        for($i=0; $i<$this->_carouselItems && $i < $this->_totalPages; $i++)
        {
            if(($start+$i) != $this->_currentPage)
            {
                $link = $view->url(array('p'=>$start+$i), 'default', false);
                $body .= " <a href=\"".($link)."\">".($start+$i)."</a>";
            }
            else
            {
                $body .= " ".($start+$i);
            }
        }

        return $body;
    }

    public function getTotalItems()
    {
        return $this->_totalItems;
    }

}

?>
