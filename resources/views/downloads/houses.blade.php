<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/app.css">
    <title>Users</title>
</head>
<body>
<div class="row">
<div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Houses Records</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody>
                    <tr>
                        <th>ID</th>
                        <th>House Name</th>
                        <th>House Capacity</th>
                    </tr>

                  @foreach($houses as $house)
                  <tr>
                  <td>{{ $house->id }}</td>
                    <td>{{ $house->name }}</td>
                    <td>{{ $house->capacity }}</td>
                  </tr>
                  @endforeach

                </tbody></table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
</div>
<script src="../js/app.js"></script>
</body>
</html>
