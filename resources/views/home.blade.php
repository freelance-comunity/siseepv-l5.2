@extends('layouts.app')
@section('title')
	Inicio
@endsection
@section('htmlheader_title')
	Home
@endsection


@section('main-content')
	<div class="container spark-screen">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Home</div>

					<div class="panel-body">
						@role('graduate')
							Bienvenido egresado
						@endrole
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
