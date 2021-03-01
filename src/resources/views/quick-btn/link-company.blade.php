<div class="btn-group">
    <a target="_BLANK" href="{{url('quotes/create?company_id='.$company->id)}}" class="btn btn-sm btn-secondary"><i class="fas fas fa-file-invoice-dollar"></i> <small>PDF</small></a>
    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>
    <div class="dropdown-menu">
        <a target="_BLANK" class="dropdown-item" href="{{url('killerquotes/create?company_id='.$company->id)}}">Killer</a>
        <a target="_BLANK" class="dropdown-item" href="{{url('quotes/create?company_id='.$company->id)}}">Pdf</a>
    </div>
</div>
