# PHP EasyReport - Create reports from PHP
EasyReport is a PHP library that allows you to make reports in PDF,
ODT (OpenOffice), and DOC (MS Word) formats using an ODT template file.

## 0. Requirements
To create PDF and DOC files, you need to install de **unoconv tool**.

## 1. Creating a template
It is very easy to create a template, all you need is openning the OpenOffice Writer (or other text writer which can create ODT files) and input the text and styles like you would see the output. You must replace the dynamic values with placeholders.

## 2. Placeholder
There are two kinds of placeholder:
### 2.1. Value placeholder
It is useful to put a text in a specific situation of a document. The format is showed below:

```{{id}}```

Example:

```Montly report of user {{name}}```

The value of the placeholder *{{name}}* will be setted from PHP.

### 2.2. Widget placeholder
To get more advanced functions, EasyReport allows the use of widgets. A widget is a piece of code to add more complex elements to the doc (tables, charts, draws...).

EasyReport is designed to allow you create or extend your own widgets. The widget placeholder format is showed below:

```{{:widgetName identifier}}```

It is easy to differenciate it due to it has a colon next to the brace. The identifier is used to send data to the widget from the PHP code. At below, an example to create a users table:

```{{:table userList}}```

Then, you must assign data to the widget from PHP.

## 3. Create the document
When we have the template, we can create so many reports as we want. The only one we need to do is instantiate the EasyReport class and call his method *create* with the required params. For example:

<pre>
$docGenerator = new DocGenerator('template-test.odt', '/tmp');
$docGenerator->create('final.doc', array('name' => 'John'));
</pre>

In the below code, it is used the template *template-test.odt* to generate a MS Word document. The second parameter of the *create* method is the data assigned to the placeholders, so every occurrence of *{{name}} placeholder will be replaced with the word *'John'*.

For widget placeholders, you can pass any kind of data (not only strings) since each widget handles the received data. For example, the table expects as data a bidimensional array to print the rows and columns:

<pre>
 $data = array(
    'visits' => array( 
        array('Nombre', 'Fecha acceso', 'Tiempo visita'),
        array('Emilio Nicolás', '20/10/2011', '5 min'),
        array('Javier López', '20/10/2011', '1m'),
        array('Adrián Mato', '19/10/2011', '2 min'),
        array('Jesús Pérez', '18/10/2011', '8 min')
));
    
$docGenerator->create('final.odt', $data)
</pre>

The code will show a table in every occurrence of the widget placeholder *{{:table visits}}*

# The MIT License (MIT)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
