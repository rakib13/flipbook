<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- In your layouts/app.blade.php or similar -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <!-- Button to trigger the modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
        Open Modal
    </button>

    <!-- The Modal -->
    @include('modal-content', ['filePath' => 'path/to/your/file.pdf'])

</body>

<script>
    // Example using jQuery and AJAX
    $('#myModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var dataId = button.data('id'); // Extract info from data-* attributes

        $.ajax({
            url: '/your-data-endpoint/' + dataId,
            method: 'GET',
            success: function(response) {
                $('#myModal .modal-body').html(response); // Inject content
            }
        });
    });
</script>

</html>
