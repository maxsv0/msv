{assign var="items" value=$menu['top']}


<!-- Static navbar -->
<nav class="navbar navbar-kica">
<div class="">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
  </div>
  <div id="navbar" class="navbar-collapse  navbar-default collapse">
    <ul class="nav navbar-nav">
    
{section name=index loop=$items}

{if $items[index].url == $page.url}
    <li class="active"><a href="{$items[index].url}">{$items[index].name}</a></li>
{else}
    <li><a href="{$items[index].url}">{$items[index].name}</a></li>
{/if}

{/section} 
    
    </ul>
  
  </div><!--/.nav-collapse -->
    
</div><!--/.container-fluid -->
</nav>
