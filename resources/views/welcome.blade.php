<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FLip Book Modal</title>
    <!-- In your layouts/app.blade.php or similar -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin-bottom: 12px;
        }

        #flipbook-wrapper {
            position: relative;
            display: inline-block;
        }

        #flipbook {
            width: 1000px;
            height: 650px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            background: #fff;
        }

        #flipbook .page {
            width: 500px;
            height: 650px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        #flipbook .page canvas {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Top control buttons */
        .controls {
            margin: 12px 0;
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        button,
        a.download-btn {
            padding: 8px 12px;
            border: none;
            background: #2e8b57;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        button:hover,
        a.download-btn:hover {
            background: #257046;
        }

        /* Navigation arrows */
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 50;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
            font-size: 22px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            user-select: none;
        }

        .nav-arrow.left {
            left: -50px;
        }

        .nav-arrow.right {
            right: -50px;
        }

        /* slider */
        #page-slider {
            width: 60%;
            margin: 20px auto 0 auto;
            display: block;
        }

        /* Bottom bar for share/download */
        .bottom-bar {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>


</head>

<body>
    <!-- Button to trigger the modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
        Open Modal
    </button>

    <!-- The Modal -->
    @include('modal-content', ['filePath' => 'pdf/git-n-github-at-glance.pdf'])

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/3/turn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>

<script>
    // Example using jQuery and AJAX
    $('#myModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        // var dataId = button.data('id'); // Extract info from data-* attributes

        $.ajax({
            url: '/Modal',
            method: 'GET',
            success: function(response) {
                $('#myModal .modal-body').html(response); // Inject content
            }
        });
    });

</script>

</html>
