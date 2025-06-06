{{--<script src="{{ asset('assets_front/js/jquery.min.js') }}"></script>--}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if($errors->any())
        @foreach ($errors->all() as $error)
            Swal.fire({
                    title: "Error",
                    text: "{{$error}}",
                    icon: "error",
                    confirmButtonText:
                        'Aceptar',
                    customClass: {
                        confirmButton: 'btn btn-outline-primary-2 btn-round'
                    },
                    buttonsStyling: false
                });
        @endforeach
    @endif

    @if(session('swal') != null)

        Swal.fire({
                  title: '{{ session('swal.title') }}',
                  text:'{{ session('swal.message') }}',
                  icon: '{{ session('swal.icon') }}',
                  confirmButtonText:
                      'Aceptar',
                  customClass: {
                      confirmButton: 'btn btn-outline-primary-2 btn-round'
                  },
                  buttonsStyling: false
              });
    @endif
</script>
