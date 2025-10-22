@section('js')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

<!-- Custom Students JavaScript -->
<script>
    // Set the AJAX URL dynamically
    studentsTableConfig.ajax.url = "{{ route('admin.students.index') }}";
</script>
<script src="{{ asset('js/admin/students/datatable.js') }}"></script>
<script src="{{ asset('js/admin/students/index.js') }}"></script>
@endsection
