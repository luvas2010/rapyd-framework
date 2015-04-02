# Data Filter Syntax #

![http://www.rapyd.com/assets/images/datafilter.gif](http://www.rapyd.com/assets/images/datafilter.gif)

## sample ##

using source, the filter can build a where clause only on fields of "artcles" table

```

  $filter = new datafilter_library();
  $filter->label = 'Article Filter';
  $filter->source('articles');
  $filter->field('input','title','Title')->attributes(array('style' => 'width:170px'));
  $filter->field('radiogroup','public','Public')->options(array("y"=>"Yes", "n"=>"No"));
  $filter->buttons('reset','search');
  $filter->build();


```


## sample 2 ##

using db object instead source(), the filter can build a where clause also on joined tables/fields


```
$filter = new datafilter_library();
$filter->label = 'Article Filter';

$filter->db->select("articles.*, authors.*");
$filter->db->from("articles");
$filter->db->join("authors","authors.author_id=articles.author_id","LEFT");

$filter->field('input','title','Title')->attributes(array('style' => 'width:170px'));
$filter->field('radiogroup','public','Public')->options(array("y"=>"Yes", "n"=>"No"));
$filter->buttons('reset','search');
$filter->build();
```


## properties ##

| **Property** | **Default Value** | **Options** | **Description** |
|:-------------|:------------------|:------------|:----------------|
| label | '' | string | label to display for the filter |


## methods ##
**$filter->source($source)**

| **$source** | - | mixed | may be a db-table name, or a  sql-query |
|:------------|:--|:------|:----------------------------------------|

**$filter->field($type, $name, $label)**

| **$type** | - | string | field type (input, checkboxgroup, checkbox, radiogroup, radio, dropdown, date)  |
|:----------|:--|:-------|:--------------------------------------------------------------------------------|
| **$name** | - | string | field name, usually the db table field to append in the where clause |
| **$label** | - | string | label to display for the field |

$filter->field($type, $name, $label)->**attributes($attributes)**

| **$attributes** | - | assoc. array | array of extra attributes to build for column |
|:----------------|:--|:-------------|:----------------------------------------------|

$filter->field($type, $name, $label)->**options($options)**

| **$options** | - | array | array of options (for field types like dropdown, radiogroup etc..) |
|:-------------|:--|:------|:-------------------------------------------------------------------|

**$filter->buttons($button1`[,$button2...]`)**

| **$button** n | - | mixed | name of buttons to build ('reset' and 'search' are the standard buttons available for datafilter) |
|:--------------|:--|:------|:--------------------------------------------------------------------------------------------------|

**$filter->build()**

build filter, fill $filter->output