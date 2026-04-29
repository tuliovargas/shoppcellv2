<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CellShopp Loja</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body class="hold-transition">
          <!-- Content Wrapper. Contains page content -->
          <div class="content-wrapper m-0">
            <!-- Content Header (Page header) -->
            <div class="content-header">
              <div class="container-fluid">
                <div class="row mb-2">
                  <div class="col-sm-6">
                    <h1 class="m-0">@yield('title')</h1>
                  </div><!-- /.col -->
                </div><!-- /.row -->
              </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                @yield('content')
            </div>
            <!-- /.content -->
          </div>
          <!-- /.content-wrapper -->
          <!-- Main Footer -->
          <footer class="main-footer">
            <!-- Default to the left -->
            <strong>Copyright &copy; {{now()->year}}</strong> Todos os direitos reservados.
          </footer>
        <!-- ./wrapper -->
        <script src="{{asset('js/app.js')}}"></script>
        @stack('scripts')
    </body>
    </html>
