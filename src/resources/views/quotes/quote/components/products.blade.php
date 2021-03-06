<div class="col-md-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">Voci inserite</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped voci">
                <thead>
                    <tr>
                        <th class="pl-2">Codice</th>
                        <th>Descrizione</th>
                        <th>Qt</th>
                        <th>Prezzo<small>(€)</small></th>
                        <th>Iva<small>(%)</small></th>
                        <th>Sconto<small>(%)</small></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="card-footer p-0">

            <div class="row">
                @if(isset($quote))
                    <div class="col">
                        <a class="btn btn-secondary btn-block btn-duplicate" title="Duplica" data-id="{{$quote->id}}" href="#"><i class="fa fa-clone"></i> Duplica</a>
                    </div>
                @endif
                <div class="col">
                    <button class="btn btn-success btn-block" id="save"><i class="fa fa-save"></i> Salva</button>
                </div>
            </div>


                @if(isset($quote) && $quote->items()->exists())
                    <textarea class="d-none" name="itemsToForm" id="itemsToForm">{!! $items !!}</textarea>
                @else
                    <textarea class="d-none" name="itemsToForm" id="itemsToForm"></textarea>
                @endif
            <button class="d-none" type="submit">Real</button>
        </div>
    </div>
</div>
