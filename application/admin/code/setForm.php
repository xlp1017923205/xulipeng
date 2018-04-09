                                    <div class="form-group">
                                        <label class=" col-sm-2 control-label" for="input-%name%">%comment%</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="%name%" value="{$data['%name%']|default=''}" placeholder="请输入%comment%内容" id="input_%name%" class="form-control" />
                                          {if condition="isset($message['%name%'])"}
                                          <label for="input-%name%" class="text-danger">{$message['%name%']}</label>
                                          {/if}
                                        </div>
                                        
                                    </div>