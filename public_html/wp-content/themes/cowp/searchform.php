<form role="search" method="get" class="search-form clear-after" action="<?php echo home_url( '/' ); ?>">
	<input type="search" class="search-field" placeholder="Buscar por…" value="<?php the_search_query(); ?>" name="s" title="Search" />
	<button class="icon-search"></button>
</form>