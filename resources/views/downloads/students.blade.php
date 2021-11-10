<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/app.css">
    <title>Students</title>
</head>
<body>
<div class="row">
<div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Students Records</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Amisssion Number</th>
                        <th>Class</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Location</th>

                    </tr>

                  @foreach($students as $student)
                  <tr>
                    <td>{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->adm_no }}</td>
                    <td>{{ $student->class }}</td>
                    <td>{{ $student->gender }}</td>
                    <td>{{ $student->dob }}</td>
                    <td>{{ $student->location }}</td>
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
