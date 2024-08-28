{{-- MODAL --}}
                            {{-- <div class="modal fade" id="UploadInacbg{{ $key }}" tabindex="-1"
                                role="dialog" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title">Upload Berkas <b>INACBG</b> :
                                                <u>{{ $item->nm_pasien }}</u>
                                            </h6>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>File Inacbg
                                                        </label>
                                                        <input type="file" class="form-control form-control"
                                                            wire:model="upload_file_inacbg.{{ $key }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="submit" class="btn btn-primary" data-dismiss="modal"
                                                wire:click="UploadInacbg('{{ $key }}', '{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')"
                                                wire:loading.remove
                                                wire:target="upload_file_inacbg.{{ $key }}">Submit
                                            </button>
                                            <div wire:loading wire:target="upload_file_inacbg.{{ $key }}">
                                                Uploading...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="UploadScan{{ $key }}" tabindex="-1"
                                role="dialog" aria-hidden="true" wire:ignore.self>
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title">Upload Berkas <b>SCAN</b> :
                                                <u>{{ $item->nm_pasien }}</u>
                                            </h6>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>File Inacbg
                                                        </label>
                                                        <input type="file" class="form-control form-control"
                                                            wire:model="upload_file_scan.{{ $key }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="submit" class="btn btn-primary" data-dismiss="modal"
                                                wire:click="UploadScan('{{ $key }}', '{{ $item->no_rawat }}', '{{ $item->no_rkm_medis }}')"
                                                wire:loading.remove
                                                wire:target="upload_file_scan.{{ $key }}">Submit
                                            </button>
                                            <div wire:loading wire:target="upload_file_scan.{{ $key }}">
                                                Uploading...</div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
