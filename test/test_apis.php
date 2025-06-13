<?php
header('Content-Type: application/json; charset=utf-8');

echo "<h1>🔍 Prueba de APIs</h1>";
echo "<h2>📊 API de Diseños:</h2>";
echo "<iframe src='api/disenos.php' width='100%' height='200'></iframe>";

echo "<h2>🎯 API de Competencias:</h2>";
echo "<iframe src='api/competencias.php' width='100%' height='200'></iframe>";

echo "<h2>🎯 API de RAPs:</h2>";
echo "<iframe src='api/raps.php' width='100%' height='200'></iframe>";

echo "<h2>🔗 Enlaces directos:</h2>";
echo "<ul>";
echo "<li><a href='api/disenos.php' target='_blank'>Ver API Diseños</a></li>";
echo "<li><a href='api/competencias.php' target='_blank'>Ver API Competencias</a></li>";
echo "<li><a href='api/raps.php' target='_blank'>Ver API RAPs</a></li>";
echo "</ul>";

echo "<br><a href='index.php'>🏠 Volver a la aplicación</a>";
?>
