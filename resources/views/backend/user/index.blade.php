<style>
  .dataTables_filter{
    text-align: right;
  }
</style>
<div class="content-wrapper" cuphtml-page title="user">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <i class="fa fa-user"></i> Users
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ URL::route('user/index') }}"><i class="fa fa-user"></i> Users</a></li>
        @if ($subPage === "view")
          <li class="active"> Index</li>
        @elseif ($subPage === "add")
          <li class="active"> Add</li>
        @elseif ($subPage === "edit")
          <li class="active"> Edit</li>
        @endif
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        @if ($subPage === "view")
          @include('backend/user/view')
        @elseif ($subPage === "add")
          @include('backend/user/add')
        @elseif ($subPage === "edit")
          @include('backend/user/edit')
        @endif
      </div><!-- /.col -->
    </div><!-- /.row -->
  </section>

</div>