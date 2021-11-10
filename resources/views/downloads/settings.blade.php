<!DOCTYPE html>
<html lang="en">
<?php

?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/app.css">
    <title>Settings</title>
</head>
<body>
<div class="row">
<div class="container">
        <div class="row mt-5">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Settings Records</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <tbody>
                    <tr>
                        <th>ID</th>
                        <th>School Name</th>
                        <th>School Motto</th>
                        <th>School Vission</th>
                        <th>Misssion Statement</th>
                        <th>Email Address</th>
                        <th>Post Office Address</th>
                        <th>Phone No.</th>
                  </tr>

                @foreach($settings as $setting)
                  <tr>
                    <td> {{ $setting->id}}</td>
                    <td>{{ $setting->sch_name}}</td>
                    <td>{{ $setting->sch_motto}}</td>
                    <td>{{ $setting->sch_vission}}</td>
                    <td>{{ $setting->sch_mission}}</td>
                    <td>{{ $setting->email}}</td>
                    <td>{{ $setting->po_address}}</td>
                    <td>{{ $setting->phone}}</td>
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
