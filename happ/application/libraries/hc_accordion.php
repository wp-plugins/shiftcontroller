<?php
class HC_Accordion {

	var $items = array();

	public function add_item( $label, $content )
	{
		$this->items[] = array( $label, $content );
	}

	public function reset()
	{
		$this->items = array();
	}

	public function generate()
	{
		$out = '';
		$out .= '<div class="accordion">';

		reset( $this->items );
		foreach( $this->items as $i )
		{
			$item_id = hc_random(12);
			$out .= '<div class="accordion-group">';
				$out .= '<div class="accordion-heading">';
					$out .= '<a class="accordion-toggle" data-toggle="collapse" href="#' . $item_id .'">';
					$out .= $i[0];
					$out .= '</a>';
				$out .= '</div>';

				$out .= '<div class="accordion-body collapse out" id="' . $item_id . '">';
					$out .= '<div class="accordion-inner">';
					$out .= $i[1];
					$out .= '</div>';
				$out .= '</div>';
			$out .= '</div>';
		}
		$out .= '</div>';

		$this->reset();
		return $out;
	}
}
