# Data Edit Syntax #


## sample ##

![http://www.rapyd.com/assets/images/dataedit.gif](http://www.rapyd.com/assets/images/dataedit.gif)

```

  $edit = new dataedit_library();
  $edit->label = 'Manage Article';
  $edit->back_url = $this->url('filtered_grid');
  
  $edit->source('articles');
  $edit->field('input','title','Title')->rule('trim','required');
  $edit->field('radiogroup','public','Public')->options(array("y"=>"Yes", "n"=>"No"));
  $edit->field('dropdown','author_id','Author')->options('SELECT author_id, firstname FROM authors')
	 ->rule('required');
  $edit->field('date','datefield','Date')->attributes(array('style'=>'width: 80px'));
  $edit->field('editor','body','Description')->rule('required');
  
  $edit->buttons('modify','save','undo','back');

  $edit->build();

  $data['head']	= $this->head();
  $data['content']= $edit->output;


```


## properties ##

| **Property** | **Default Value** | **Options** | **Description** |
|:-------------|:------------------|:------------|:----------------|
| label | '' | string | label to display for the filter |
| back\_url | '' | string | url of page where to go back (when we click on back button)|

## methods ##
**$edit->source($source)**

| **$source** | - | mixed | may be a "datamodel" object (or extended one), or the name of a db table (in this case dataedit will instance a new datamodel for us) |
|:------------|:--|:------|:--------------------------------------------------------------------------------------------------------------------------------------|

**$edit->field($type, $name, $label)**

| **$type** | - | string | field type (input, password, checkboxgroup, checkbox, radiogroup, radio, dropdown, date, editor) |
|:----------|:--|:-------|:-------------------------------------------------------------------------------------------------|
| **$name** | - | string | field name, usually the db table field to append in the where clause |
| **$label** | - | string | label to display for the field |

$edit->field($type, $name, $label)->**attributes($attributes)**

| **$attributes** | - | assoc. array | array of extra attributes to build for column |
|:----------------|:--|:-------------|:----------------------------------------------|

$edit->field($type, $name, $label)->**rule($rule)**

| **$rule** | - | mixed | $rule can be 'required' or can be the name of a custom or native php function (like trim), it's possible to pass an array instead a single rule, or use a serialized sintax like 'required|trim' using a pipe as separator |
|:----------|:--|:------|:---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|


$edit->field($type, $name, $label)->**options($options)**

| **$options** | - | array | array of options (for field types like dropdown, radiogroup etc..) |
|:-------------|:--|:------|:-------------------------------------------------------------------|

**$edit->buttons($button1`[,$button2...]`)**

| **$button** n | - | mixed | name of buttons to build ('modify','save','undo' and 'back' are the standard buttons available for dataedit) |
|:--------------|:--|:------|:-------------------------------------------------------------------------------------------------------------|

**$edit->build()**

build dataedit, fill $edit->output