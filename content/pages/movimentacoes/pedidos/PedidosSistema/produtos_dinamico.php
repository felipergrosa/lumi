<div class="form-group row" id="cadastro_pedidos_produtos_form2">
                        <div class="col-xs-2 col-md-2">
                            <label>Código</label>
                        </div>
                        <div class="col-xs-3 col-md-3">
                            <label>Nome (Digite para buscar)</label>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <label>Valor</label>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <label>Qt</label>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <label>Subt.</label>
                        </div>
                        <div class="col-xs-1 text-center">
                            <span class="fa fa-trash" style="font-size: 2.2em; cursor:pointer;"></span>
                        </div>
                    </div>
<?php
                        for($i=1; $i<10; $i++){
                            ?>
                            <div class="form-group row" id="dump_produto<?php echo $i; ?>">
                        <div class="col-xs-2 col-md-2">
                            <input type="text" class="form-control" id="vendas_nova_venda_produto_id" name="vendas_nova_venda_produto_id" 
                            onkeypress="ProximoCampo('dump_produto<?php echo $i; ?>', 'qt', event);"
                            ondblclick="console.log('dblclick');" readonly
                            />
                        </div>
                        <div class="col-xs-3 col-md-3">
                            <input type="text" class="form-control" id="vendas_nova_venda_produto_nome" name="vendas_nova_venda_produto_nome"
                            onkeyup="BuscaProdutoGatilho(this, this.value, 'dump_produto<?php echo $i; ?>');"
                        />
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <input type="text" class="form-control" id="vendas_nova_venda_produto_valor" name="vendas_nova_venda_produto_valor" readonly/>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <input type="number" class="form-control" id="vendas_nova_venda_produto_qt" name="vendas_nova_venda_produto_qt" onblur="javascript: RecalculaValor(this, this.value);"
                            onkeypress="ProximoCampo('dump_produto<?php echo $i+1; ?>', 'id', event);"/>
                        </div>
                        <div class="col-xs-2 col-md-2">
                            <input type="text" class="form-control vendas_nova_venda_produto_subtot" id="vendas_nova_venda_produto_subtot" name="vendas_nova_venda_produto_subtot" readonly/>
                        </div>
                        <div class="col-xs-1 text-center">
                            <span class="fa fa-trash" style="font-size: 2.2em; cursor:pointer;" onclick="javascript: DeleteLinha(this);"></span>
                        </div>
                    </div>
                            <?php
                        }
                    ?>