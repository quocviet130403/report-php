<?php

$data = $_POST['catChart'];

$files = glob('tmp/*'); // get all file names
foreach($files as $file){ // iterate files
    if(is_file($file)) {
        unlink($file); // delete file
    }
}


define('UPLOAD_DIR', 'tmp/');
$img = $_POST['catChart'];
$img = str_replace('data:image/svg+xml;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$data = base64_decode($img);
$file = UPLOAD_DIR . uniqid() . '.svg';
file_put_contents($file, $data);

echo "<img src='/test/".$file."' />";


var_dump($data);

?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title></title>
</head>
<body>
<div id="svg-container">
    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" width="850" height="440"><g class="subplot xy"><rect x="78" y="78" width="694" height="284" style="stroke-width: 0px; fill: #ffffff; fill-opacity: 1;"></rect><g></g><g></g><g></g><g></g><svg preserveAspectRatio="none" x="80" y="80" width="690" height="280" style="fill: none;" viewBox="0 0 690 280"><g class="errorbars" style="stroke: #000000;"></g><g class="errorbars" style="stroke: #000000;"></g><g class="errorbars" style="stroke: #000000;"></g><g class="errorbars" style="stroke: #000000;"></g><g class="errorbars" style="stroke: #000000;"></g><g class="trace scatter" style="stroke-miterlimit: 2; opacity: 1;"><path class="js-fill" data-curve="0" d="M0,275.87L14.08,247.04L28.16,232.85L42.24,253.86L56.33,246.47L70.41,243.89L84.49,217.25L98.57,217.82L112.65,225.6L126.73,241.67L140.82,253.84L154.9,216.99L168.98,207.33L183.06,212.18L197.14,251.33L211.22,242.07L225.31,234.25L239.39,221.58L253.47,217.92L267.55,228.46L281.63,224.71L295.71,253.29L309.8,202.84L323.88,225.91L337.96,229.59L352.04,216.11L366.12,225.59L380.2,222.05L394.29,194.73L408.37,209.7L422.45,189.5L436.53,242.94L450.61,191.95L464.69,200.14L478.78,204.29L492.86,184.43L506.94,231L521.02,223.42L535.1,176.94L549.18,179.33L563.27,182.01L577.35,193.65L591.43,208.45L605.51,226.7L619.59,191.43L633.67,173.51L647.76,176.67L661.84,182.12L675.92,190.75L690,163.75L690,280L0,280Z" style="stroke-width: 0px; fill: #df3f1a; fill-opacity: 1;"></path><path class="js-fill" d="M690,158.64L675.92,146.27L661.84,123.59L647.76,133.24L633.67,129.54L619.59,184.78L605.51,173.37L591.43,191.32L577.35,139.98L563.27,140.87L549.18,120.83L535.1,165.83L521.02,202.89L506.94,174.71L492.86,183.88L478.78,176.98L464.69,168.83L450.61,147.56L436.53,218.62L422.45,157.63L408.37,169.32L394.29,193.1L380.2,198.8L366.12,174.23L352.04,174.58L337.96,220.43L323.88,218.74L309.8,197.31L295.71,210.27L281.63,198.01L267.55,206.3L253.47,178.69L239.39,177.23L225.31,180.65L211.22,236.26L197.14,193.08L183.06,209.16L168.98,170.28L154.9,174.01L140.82,217.41L126.73,204.06L112.65,201.15L98.57,205.47L84.49,213.49L70.41,206.95L56.33,195.46L42.24,224.24L28.16,202.11L14.08,212.84L0,258.04L0,275.87L14.08,247.04L28.16,232.85L42.24,253.86L56.33,246.47L70.41,243.89L84.49,217.25L98.57,217.82L112.65,225.6L126.73,241.67L140.82,253.84L154.9,216.99L168.98,207.33L183.06,212.18L197.14,251.33L211.22,242.07L225.31,234.25L239.39,221.58L253.47,217.92L267.55,228.46L281.63,224.71L295.71,253.29L309.8,202.84L323.88,225.91L337.96,229.59L352.04,216.11L366.12,225.59L380.2,222.05L394.29,194.73L408.37,209.7L422.45,189.5L436.53,242.94L450.61,191.95L464.69,200.14L478.78,204.29L492.86,184.43L506.94,231L521.02,223.42L535.1,176.94L549.18,179.33L563.27,182.01L577.35,193.65L591.43,208.45L605.51,226.7L619.59,191.43L633.67,173.51L647.76,176.67L661.84,182.12L675.92,190.75L690,163.75Z" style="stroke-width: 0px; fill: #3bcc2d; fill-opacity: 1;"></path><path class="js-line" d="M0,275.87L14.08,247.04L28.16,232.85L42.24,253.86L56.33,246.47L70.41,243.89L84.49,217.25L98.57,217.82L112.65,225.6L126.73,241.67L140.82,253.84L154.9,216.99L168.98,207.33L183.06,212.18L197.14,251.33L211.22,242.07L225.31,234.25L239.39,221.58L253.47,217.92L267.55,228.46L281.63,224.71L295.71,253.29L309.8,202.84L323.88,225.91L337.96,229.59L352.04,216.11L366.12,225.59L380.2,222.05L394.29,194.73L408.37,209.7L422.45,189.5L436.53,242.94L450.61,191.95L464.69,200.14L478.78,204.29L492.86,184.43L506.94,231L521.02,223.42L535.1,176.94L549.18,179.33L563.27,182.01L577.35,193.65L591.43,208.45L605.51,226.7L619.59,191.43L633.67,173.51L647.76,176.67L661.84,182.12L675.92,190.75L690,163.75" style="stroke-width: 2px; stroke: #df3f1a; stroke-opacity: 0.7; fill: none;"></path></g><g class="trace scatter" style="stroke-miterlimit: 2; opacity: 1;"><path class="js-fill" d="M690,157.48L675.92,114.54L661.84,121.44L647.76,111.26L633.67,86.45L619.59,164.23L605.51,163.41L591.43,177.25L577.35,97.66L563.27,104.93L549.18,84.07L535.1,137.52L521.02,188.85L506.94,157.35L492.86,127.21L478.78,152.16L464.69,168.3L450.61,136.94L436.53,191.53L422.45,138L408.37,152.2L394.29,178.6L380.2,156.43L366.12,150.86L352.04,125.13L337.96,185.13L323.88,210.61L309.8,181.22L295.71,186.51L281.63,164.87L267.55,153.69L253.47,173.58L239.39,160.57L225.31,126.3L211.22,206.2L197.14,166.42L183.06,179.71L168.98,141.35L154.9,170.36L140.82,162.39L126.73,179.75L112.65,163.55L98.57,180.8L84.49,205.87L70.41,152.79L56.33,147.15L42.24,172.22L28.16,166.62L14.08,211.36L0,217.79L0,258.04L14.08,212.84L28.16,202.11L42.24,224.24L56.33,195.46L70.41,206.95L84.49,213.49L98.57,205.47L112.65,201.15L126.73,204.06L140.82,217.41L154.9,174.01L168.98,170.28L183.06,209.16L197.14,193.08L211.22,236.26L225.31,180.65L239.39,177.23L253.47,178.69L267.55,206.3L281.63,198.01L295.71,210.27L309.8,197.31L323.88,218.74L337.96,220.43L352.04,174.58L366.12,174.23L380.2,198.8L394.29,193.1L408.37,169.32L422.45,157.63L436.53,218.62L450.61,147.56L464.69,168.83L478.78,176.98L492.86,183.88L506.94,174.71L521.02,202.89L535.1,165.83L549.18,120.83L563.27,140.87L577.35,139.98L591.43,191.32L605.51,173.37L619.59,184.78L633.67,129.54L647.76,133.24L661.84,123.59L675.92,146.27L690,158.64Z" style="stroke-width: 0px; fill: #30bfaf; fill-opacity: 1;"></path><path class="js-line" d="M0,258.04L14.08,212.84L28.16,202.11L42.24,224.24L56.33,195.46L70.41,206.95L84.49,213.49L98.57,205.47L112.65,201.15L126.73,204.06L140.82,217.41L154.9,174.01L168.98,170.28L183.06,209.16L197.14,193.08L211.22,236.26L225.31,180.65L239.39,177.23L253.47,178.69L267.55,206.3L281.63,198.01L295.71,210.27L309.8,197.31L323.88,218.74L337.96,220.43L352.04,174.58L366.12,174.23L380.2,198.8L394.29,193.1L408.37,169.32L422.45,157.63L436.53,218.62L450.61,147.56L464.69,168.83L478.78,176.98L492.86,183.88L506.94,174.71L521.02,202.89L535.1,165.83L549.18,120.83L563.27,140.87L577.35,139.98L591.43,191.32L605.51,173.37L619.59,184.78L633.67,129.54L647.76,133.24L661.84,123.59L675.92,146.27L690,158.64" style="stroke-width: 2px; stroke: #3bcc2d; stroke-opacity: 0.7; fill: none;"></path></g><g class="trace scatter" style="stroke-miterlimit: 2; opacity: 1;"><path class="js-fill" d="M690,113.39L675.92,96.45L661.84,93.61L647.76,86.69L633.67,77.58L619.59,115.91L605.51,116.22L591.43,118.85L577.35,70.64L563.27,89.6L549.18,27.48L535.1,110.45L521.02,147.22L506.94,100.38L492.86,96.37L478.78,107.03L464.69,152.89L450.61,129.64L436.53,190.41L422.45,137.88L408.37,133.06L394.29,147.27L380.2,143.31L366.12,126.78L352.04,117.11L337.96,136.9L323.88,164.89L309.8,179.57L295.71,185.94L281.63,106.73L267.55,147.15L253.47,159.3L239.39,157.88L225.31,88.64L211.22,184.12L197.14,140.99L183.06,121.34L168.98,83.98L154.9,123.96L140.82,134.93L126.73,123.86L112.65,160.88L98.57,179.57L84.49,164.97L70.41,149.92L56.33,135.54L42.24,159.03L28.16,145.58L14.08,163.28L0,170.02L0,217.79L14.08,211.36L28.16,166.62L42.24,172.22L56.33,147.15L70.41,152.79L84.49,205.87L98.57,180.8L112.65,163.55L126.73,179.75L140.82,162.39L154.9,170.36L168.98,141.35L183.06,179.71L197.14,166.42L211.22,206.2L225.31,126.3L239.39,160.57L253.47,173.58L267.55,153.69L281.63,164.87L295.71,186.51L309.8,181.22L323.88,210.61L337.96,185.13L352.04,125.13L366.12,150.86L380.2,156.43L394.29,178.6L408.37,152.2L422.45,138L436.53,191.53L450.61,136.94L464.69,168.3L478.78,152.16L492.86,127.21L506.94,157.35L521.02,188.85L535.1,137.52L549.18,84.07L563.27,104.93L577.35,97.66L591.43,177.25L605.51,163.41L619.59,164.23L633.67,86.45L647.76,111.26L661.84,121.44L675.92,114.54L690,157.48Z" style="stroke-width: 0px; fill: #337fb9; fill-opacity: 1;"></path><path class="js-line" d="M0,217.79L14.08,211.36L28.16,166.62L42.24,172.22L56.33,147.15L70.41,152.79L84.49,205.87L98.57,180.8L112.65,163.55L126.73,179.75L140.82,162.39L154.9,170.36L168.98,141.35L183.06,179.71L197.14,166.42L211.22,206.2L225.31,126.3L239.39,160.57L253.47,173.58L267.55,153.69L281.63,164.87L295.71,186.51L309.8,181.22L323.88,210.61L337.96,185.13L352.04,125.13L366.12,150.86L380.2,156.43L394.29,178.6L408.37,152.2L422.45,138L436.53,191.53L450.61,136.94L464.69,168.3L478.78,152.16L492.86,127.21L506.94,157.35L521.02,188.85L535.1,137.52L549.18,84.07L563.27,104.93L577.35,97.66L591.43,177.25L605.51,163.41L619.59,164.23L633.67,86.45L647.76,111.26L661.84,121.44L675.92,114.54L690,157.48" style="stroke-width: 2px; stroke: #30bfaf; stroke-opacity: 0.7; fill: none;"></path></g><g class="trace scatter" style="stroke-miterlimit: 2; opacity: 1;"><path class="js-fill" d="M690,58.22L675.92,81.07L661.84,49.24L647.76,68.51L633.67,58.63L619.59,103.24L605.51,72.14L591.43,93.38L577.35,14L563.27,50.77L549.18,16.12L535.1,95.98L521.02,113.19L506.94,71.26L492.86,80.51L478.78,84.94L464.69,125.1L450.61,106.98L436.53,161.85L422.45,94.14L408.37,96.37L394.29,94.19L380.2,97.75L366.12,97.77L352.04,109.66L337.96,98.75L323.88,115.54L309.8,149.97L295.71,168.93L281.63,91.43L267.55,93.86L253.47,105.98L239.39,110.84L225.31,59.15L211.22,145.29L197.14,91.15L183.06,69.09L168.98,77.56L154.9,116.14L140.82,84.46L126.73,109.91L112.65,137.12L98.57,151.57L84.49,153.43L70.41,129.87L56.33,93.64L42.24,157.12L28.16,109.88L14.08,136.91L0,133.88L0,170.02L14.08,163.28L28.16,145.58L42.24,159.03L56.33,135.54L70.41,149.92L84.49,164.97L98.57,179.57L112.65,160.88L126.73,123.86L140.82,134.93L154.9,123.96L168.98,83.98L183.06,121.34L197.14,140.99L211.22,184.12L225.31,88.64L239.39,157.88L253.47,159.3L267.55,147.15L281.63,106.73L295.71,185.94L309.8,179.57L323.88,164.89L337.96,136.9L352.04,117.11L366.12,126.78L380.2,143.31L394.29,147.27L408.37,133.06L422.45,137.88L436.53,190.41L450.61,129.64L464.69,152.89L478.78,107.03L492.86,96.37L506.94,100.38L521.02,147.22L535.1,110.45L549.18,27.48L563.27,89.6L577.35,70.64L591.43,118.85L605.51,116.22L619.59,115.91L633.67,77.58L647.76,86.69L661.84,93.61L675.92,96.45L690,113.39Z" style="stroke-width: 0px; fill: #a4477d; fill-opacity: 1;"></path><path class="js-line" d="M0,170.02L14.08,163.28L28.16,145.58L42.24,159.03L56.33,135.54L70.41,149.92L84.49,164.97L98.57,179.57L112.65,160.88L126.73,123.86L140.82,134.93L154.9,123.96L168.98,83.98L183.06,121.34L197.14,140.99L211.22,184.12L225.31,88.64L239.39,157.88L253.47,159.3L267.55,147.15L281.63,106.73L295.71,185.94L309.8,179.57L323.88,164.89L337.96,136.9L352.04,117.11L366.12,126.78L380.2,143.31L394.29,147.27L408.37,133.06L422.45,137.88L436.53,190.41L450.61,129.64L464.69,152.89L478.78,107.03L492.86,96.37L506.94,100.38L521.02,147.22L535.1,110.45L549.18,27.48L563.27,89.6L577.35,70.64L591.43,118.85L605.51,116.22L619.59,115.91L633.67,77.58L647.76,86.69L661.84,93.61L675.92,96.45L690,113.39" style="stroke-width: 2px; stroke: #337fb9; stroke-opacity: 0.7; fill: none;"></path></g><g class="trace scatter" style="stroke-miterlimit: 2; opacity: 1;"><path class="js-line" d="M0,133.88L14.08,136.91L28.16,109.88L42.24,157.12L56.33,93.64L70.41,129.87L84.49,153.43L98.57,151.57L112.65,137.12L126.73,109.91L140.82,84.46L154.9,116.14L168.98,77.56L183.06,69.09L197.14,91.15L211.22,145.29L225.31,59.15L239.39,110.84L253.47,105.98L267.55,93.86L281.63,91.43L295.71,168.93L309.8,149.97L323.88,115.54L337.96,98.75L352.04,109.66L366.12,97.77L380.2,97.75L394.29,94.19L408.37,96.37L422.45,94.14L436.53,161.85L450.61,106.98L464.69,125.1L478.78,84.94L492.86,80.51L506.94,71.26L521.02,113.19L535.1,95.98L549.18,16.12L563.27,50.77L577.35,14L591.43,93.38L605.51,72.14L619.59,103.24L633.67,58.63L647.76,68.51L661.84,49.24L675.92,81.07L690,58.22" style="stroke-width: 2px; stroke: #a4477d; stroke-opacity: 0.7; fill: none;"></path></g></svg><g></g><path class="crisp" d="M77,362.5h696M77,77.5h696" style="fill: none; stroke-width: 1px; stroke: #000000; stroke-opacity: 1;"></path><path class="crisp" d="M77.5,78v284M772.5,78v284" stroke-width="1px" style="fill: none; stroke: #000000; stroke-opacity: 1;"></path><g></g><g><path class="xtick ticks crisp" d="M80,363v5" transform="translate(0,0)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="xtick ticks crisp" d="M80,363v5" transform="translate(138,0)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="xtick ticks crisp" d="M80,363v5" transform="translate(276,0)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="xtick ticks crisp" d="M80,363v5" transform="translate(414,0)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="xtick ticks crisp" d="M80,363v5" transform="translate(552,0)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="xtick ticks crisp" d="M80,363v5" transform="translate(690,0)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><text class="xtick" x="80" y="381" transform="translate(0,0)" text-anchor="middle" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0</tspan></text><text class="xtick" x="80" y="381" transform="translate(138,0)" text-anchor="middle" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.2</tspan></text><text class="xtick" x="80" y="381" transform="translate(276,0)" text-anchor="middle" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.4</tspan></text><text class="xtick" x="80" y="381" transform="translate(414,0)" text-anchor="middle" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.6</tspan></text><text class="xtick" x="80" y="381" transform="translate(552,0)" text-anchor="middle" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.8</tspan></text><text class="xtick" x="80" y="381" transform="translate(690,0)" text-anchor="middle" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">1</tspan></text><path class="ytick ticks crisp" d="M77,80h-5" transform="translate(0,280)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="ytick ticks crisp" d="M77,80h-5" transform="translate(0,221.46)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="ytick ticks crisp" d="M77,80h-5" transform="translate(0,162.93)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="ytick ticks crisp" d="M77,80h-5" transform="translate(0,104.39)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><path class="ytick ticks crisp" d="M77,80h-5" transform="translate(0,45.85)" style="stroke: #000000; stroke-opacity: 1; stroke-width: 1px;"></path><text class="ytick" x="71" y="86" transform="translate(0,280)" text-anchor="end" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0</tspan></text><text class="ytick" x="71" y="86" transform="translate(0,221.46)" text-anchor="end" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.1</tspan></text><text class="ytick" x="71" y="86" transform="translate(0,162.93)" text-anchor="end" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.2</tspan></text><text class="ytick" x="71" y="86" transform="translate(0,104.39)" text-anchor="end" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.3</tspan></text><text class="ytick" x="71" y="86" transform="translate(0,45.85)" text-anchor="end" style="font-size: 12px; fill: #000000; fill-opacity: 1;"><tspan class="nl">0.4</tspan></text></g><g></g></g><g><rect class="drag nsewdrag cursor-crosshair" x="80" y="80" width="690" height="280" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag nwdrag cursor-nw-resize" x="60" y="60" width="20" height="20" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag nedrag cursor-ne-resize" x="770" y="60" width="20" height="20" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag swdrag cursor-sw-resize" x="60" y="360" width="20" height="20" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag sedrag cursor-se-resize" x="770" y="360" width="20" height="20" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag ewdrag cursor-ew-resize" x="149" y="362.5" width="552" height="20" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag wdrag cursor-w-resize" x="80" y="362.5" width="69" height="20" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag edrag cursor-e-resize" x="701" y="362.5" width="69" height="20" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag nsdrag cursor-ns-resize" x="57.5" y="108" width="20" height="224" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag sdrag cursor-s-resize" x="57.5" y="332" width="20" height="28" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect><rect class="drag ndrag cursor-n-resize" x="57.5" y="80" width="20" height="28" data-subplot="xy" style="fill: #000000; opacity: 0; stroke-width: 0px;"></rect></g><g class="infolayer"><text class="gtitle" data-unformatted="Its All About the Graphs" x="425" y="40" text-anchor="middle" xml:space="preserve" style="font-size: 16.8px; fill: #000000; opacity: 1; pointer-events: all;">Its All About the Graphs</text><text class="xtitle" data-unformatted="Click to enter X axis title" x="425" y="413.2" text-anchor="middle" xml:space="preserve" style="font-size: 14.4px; fill: #000000; opacity: 0; pointer-events: all;">Click to enter X axis title</text><text class="ytitle" data-unformatted="Click to enter Y axis title" x="41.2" y="220" text-anchor="middle" transform="rotate(-90,41.2,220) translate(0, 0)" xml:space="preserve" style="font-size: 14.4px; fill: #000000; opacity: 0; pointer-events: all;">Click to enter Y axis title</text><svg class="legend" x="84" y="80" width="117" height="109"><rect class="bg" style="stroke: #ffffff; stroke-opacity: 1; fill: #ffffff; opacity: 1; stroke-width: 1px;" x="0.5" y="0.5" width="116" height="108"></rect><g class="traces" transform="translate(1,14.171875)"><g class="legendsymbols" style="opacity: 1;"><g class="legendpoints"></g><path class="js-fill" data-curve="0" d="M5,0h30v6h-30z" style="stroke-width: 0px; fill: #df3f1a; fill-opacity: 1;"></path><path class="js-line" d="M5,0h30" style="stroke-width: 2px; stroke: #df3f1a; stroke-opacity: 0.7; fill: none;"></path></g><text class="legendtext text-0" x="40" y="4.65625" data-unformatted="Ruby Rouge" xml:space="preserve" style="text-anchor: start; font-size: 12px; fill: #000000; opacity: 1; pointer-events: all;">Ruby Rouge</text></g><g class="traces" transform="translate(1,33.515625)"><g class="legendsymbols" style="opacity: 1;"><g class="legendpoints"></g><path class="js-fill" data-curve="1" d="M5,0h30v6h-30z" style="stroke-width: 0px; fill: #3bcc2d; fill-opacity: 1;"></path><path class="js-line" d="M5,0h30" style="stroke-width: 2px; stroke: #3bcc2d; stroke-opacity: 0.7; fill: none;"></path></g><text class="legendtext text-1" x="40" y="4.65625" data-unformatted="Green Apple" xml:space="preserve" style="text-anchor: start; font-size: 12px; fill: #000000; opacity: 1; pointer-events: all;">Green Apple</text></g><g class="traces" transform="translate(1,52.859375)"><g class="legendsymbols" style="opacity: 1;"><g class="legendpoints"></g><path class="js-fill" data-curve="2" d="M5,0h30v6h-30z" style="stroke-width: 0px; fill: #30bfaf; fill-opacity: 1;"></path><path class="js-line" d="M5,0h30" style="stroke-width: 2px; stroke: #30bfaf; stroke-opacity: 0.7; fill: none;"></path></g><text class="legendtext text-2" x="40" y="4.65625" data-unformatted="Turtoise" xml:space="preserve" style="text-anchor: start; font-size: 12px; fill: #000000; opacity: 1; pointer-events: all;">Turtoise</text></g><g class="traces" transform="translate(1,72.203125)"><g class="legendsymbols" style="opacity: 1;"><g class="legendpoints"></g><path class="js-fill" data-curve="3" d="M5,0h30v6h-30z" style="stroke-width: 0px; fill: #337fb9; fill-opacity: 1;"></path><path class="js-line" d="M5,0h30" style="stroke-width: 2px; stroke: #337fb9; stroke-opacity: 0.7; fill: none;"></path></g><text class="legendtext text-3" x="40" y="4.65625" data-unformatted="Blue Lemon" xml:space="preserve" style="text-anchor: start; font-size: 12px; fill: #000000; opacity: 1; pointer-events: all;">Blue Lemon</text></g><g class="traces" transform="translate(1,91.546875)"><g class="legendsymbols" style="opacity: 1;"><g class="legendpoints"></g><path class="js-fill" data-curve="4" d="M5,0h30v6h-30z" style="stroke-width: 0px; fill: #a4477d; fill-opacity: 1;"></path><path class="js-line" d="M5,0h30" style="stroke-width: 2px; stroke: #a4477d; stroke-opacity: 0.7; fill: none;"></path></g><text class="legendtext text-4" x="40" y="4.65625" data-unformatted="Persia" xml:space="preserve" style="text-anchor: start; font-size: 12px; fill: #000000; opacity: 1; pointer-events: all;">Persia</text></g></svg></g><g class="hoverlayer"></g></svg>
</div>

<canvas id="canvas" width="800" height="400"></canvas>
<div id="png-container"></div>


<script>
    var svgString = new XMLSerializer().serializeToString(document.querySelector('svg'));

    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");
    var DOMURL = self.URL || self.webkitURL || self;
    var img = new Image();
    var svg = new Blob([svgString], {type: "image/svg+xml;charset=utf-8"});
    var url = DOMURL.createObjectURL(svg);
    img.onload = function() {
        ctx.drawImage(img, 0, 0);
        var png = canvas.toDataURL("image/png");
        document.querySelector('#png-container').innerHTML = '<img src="'+png+'"/>';
        DOMURL.revokeObjectURL(png);
    };
    img.src = url;

</script>

</body>
</html>