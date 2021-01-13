@if(KillerQuote\App\Models\KillerQuote::where('company_id', $company->id)->exists())
    <div class="tab-pane" id="killerquotes">
        @can('killerquotes.read')

            @php
                $kqs = KillerQuote\App\Models\KillerQuote::where('company_id', $company->id)->get();
            @endphp

            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Numero</th>
                            <th>Data</th>
                            <th>Importo</th>
                            <th>Accettato</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kqs as $kq)
                            <tr>
                                <td>{{$kq->numero}}/{{$kq->created_at->format('Y')}}</td>
                                <td>{{$kq->created_at->format('d/m/Y')}}</td>
                                <td>{{$kq->importo}}</td>
                                <td>
                                    @if($kq->accepted === null)
                                        <span class="badge badge-default">In attesa</span>
                                    @else
                                        @if($kq->accepted)
                                            <span class="badge badge-success">Accettato</span>
                                        @else
                                            <span class="badge badge-danger">Rifiutato</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url("killerquotes/{$kq->id}/pdf") }}" title="Esporta PDF" class="btn btn-primary btn-icon btn-sm"><i class="fa fa-file-pdf"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        @endcan
    </div>
@endif
