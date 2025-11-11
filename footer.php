 <footer>
        <p>&copy; 2025 segundo Parcial Programación Web 2</p>
    </footer>

    <?php
        // 4. Liberar el resultado y cerrar la conexión
        mysqli_free_result($resultado);
        mysqli_close($conexion);
    ?>
</body>
</html>