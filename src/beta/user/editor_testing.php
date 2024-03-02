<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>

 

<script src="https://beta.nogd.no/vendor/tinymce/js/tinymce/tinymce.min.js"></script>

    <script>
      tinymce.init({
        selector: '#composer_text_4',
		
      });
    </script>

  </head>

  <body>
  <h1>TinyMCE Editor</h1>
    <form method="post">
      <textarea id="composer_text_4" rows="100">
This is testing<br/>
This is testing<br/>
This is testing <br/>
This is testing<br/>
This is testing<br/>
This is testing <br/>
This is testing<br/>
This is testing<br/>
This is testing <br/>
This is testing<br/>
This is testing<br/>
This is testing <br/>
<li>Hello</li>
<li>Hello</li>
<li>Hello</li>
<li>Hello</li>
<li>Hello</li>
<li>Hello</li>

 </textarea>
    </form>
	
	
	<script>
 
 
 $(document).ready(function() {
  var editorContent = $('#composer_text_4').val(); // Get the content of the textarea

  // Split the content into paragraphs
  var paragraphs = editorContent.split(/\n{2,}/);

  var lineCount = 0;
  var output = '';

  // Iterate over each paragraph
  for (var i = 0; i < paragraphs.length; i++) {
    var paragraph = paragraphs[i];
    var paragraphLines = paragraph.split(/\r?\n/); // Split the paragraph into lines

    // Iterate over each line in the paragraph and append it to the output string
    for (var j = 0; j < paragraphLines.length; j++) {
      var line = paragraphLines[j].trim();
      if (line !== '') { // Skip empty lines
        output += line + '\n';
        lineCount++;

        // Add a dotted line after every 5 lines
        if (lineCount % 5 === 0 && j < paragraphLines.length - 1) {
          output += "<br/>........................................................................................................................................................................................................................................................................<br/>";
          lineCount++;
        }
      }
    }
  }

  // Update the textarea with the modified content
  $('#composer_text_4').val(output);
});


</script>



	
	
  </body>
</html>
