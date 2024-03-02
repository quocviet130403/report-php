 <script src="https://beta.nogd.no/vendor/jquery-3.4.1/jquery.min.js"></script>
<script>


$(document).ready(function() {


// it is working but not for paragraph calculating one line


function add_dotted_line(idname)
{

  var div_txt = $('#'+idname).val(); // Get the content of the textarea

  // Split the content into an array of lines based on displayed line breaks
  var lines = div_txt.split(/\r?\n/);
  var output = '';

  // Iterate over each line and append it to the output string
  for (var i = 0; i < lines.length; i++) {
    output += lines[i] + '\n';

    // Add a dotted line after every 5 lines
    if ((i + 1) % 5 === 0 && (i + 1) !== lines.length) {
      output += "........................................................................................................................................................................................................................................................................\n";
    }
  }

  // Update the textarea with the modified content
  $('#'+idname).val(output);
  
  }
  
	add_dotted_line("composer_text_1");
	add_dotted_line("composer_text_2");
	add_dotted_line("composer_text_3");
	add_dotted_line("composer_text_4");
	add_dotted_line("composer_text_5");
	add_dotted_line("composer_text_6");
	add_dotted_line("composer_text_7");
	add_dotted_line("composer_text_8");
	add_dotted_line("composer_text_9");
	add_dotted_line("composer_text_10");
	add_dotted_line("composer_text_11");
	add_dotted_line("composer_text_12");
	add_dotted_line("composer_text_13");
	add_dotted_line("composer_text_14");
	add_dotted_line("composer_text_15");
  
});

 

</script>

<style>
.dotted-line {
  border-top: 1px dotted black;
 
}
</style>