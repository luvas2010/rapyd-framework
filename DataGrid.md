# Data Grid Syntax #


## sample ##

![http://www.rapyd.com/assets/images/datagrid.gif](http://www.rapyd.com/assets/images/datagrid.gif)

```

  $grid = new datagrid_library();
  $grid->label = 'Article List';
  $grid->per_page = 10;

  $grid->source('articles');
  $grid->column('article_id','ID',true)->url('edit?show={article_id}','detail.gif');
  $grid->column('title','Title');
  $grid->column('body','Body')->callback('escape');

  $grid->build();

  $data['head']   = $this->head();
  $data['content']= $grid->output;

```


## sample 2 ##

using a DataFilter instance as source it's easy to build filtered-grid

```

  $grid = new datagrid_library();
  $grid->label = 'Article List';
  $grid->per_page = 10;

  $grid->source($filter);
  ..

```

## properties ##


| **Property** | **Default Value** | **Options** | **Description** |
|:-------------|:------------------|:------------|:----------------|
| label | '' | string | label to display for the grid |
| per\_page | 10000 | integer | the number of records to display per page |



## methods ##

**$grid->source($source)**

| **$source** | '' | mixed | may be a db-table name, a sql-query, a datafilter object, an associative array matrix |
|:------------|:---|:------|:--------------------------------------------------------------------------------------|

**$grid->column($pattern, $label, $is\_orderby)**

| **$pattern** | '' | string | field pattern, field name, or content of cells |
|:-------------|:---|:-------|:-----------------------------------------------|
| **$label** | '' | string | column label |
| **$orderby** | false | boolean | if column is sortable |

**$grid->build()**
build grid, fill $grid->output