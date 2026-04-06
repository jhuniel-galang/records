<div class="mb-4">
    <h2>Document Details</h2>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><?php echo htmlspecialchars($document->file_name); ?></h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">ID</th>
                        <td><?php echo $document->id; ?></td>
                    </tr>
                    <tr>
                        <th>File Name</th>
                        <td><?php echo htmlspecialchars($document->file_name); ?></td>
                    </tr>
                    <tr>
                        <th>School</th>
                        <td><?php echo htmlspecialchars($document->school_name); ?></td>
                    </tr>
                    <tr>
                        <th>Document Type</th>
                        <td>
                            <span class="badge bg-info"><?php echo htmlspecialchars($document->document_type); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>File Type</th>
                        <td><?php echo strtoupper($document->file_type); ?></td>
                    </tr>
                    <tr>
                        <th>File Size</th>
                        <td><?php echo $file_size_formatted; ?></td>
                    </tr>
                    <tr>
                        <th>Remarks</th>
                        <td><?php echo nl2br(htmlspecialchars($document->remarks)); ?></td>
                    </tr>
                    <tr>
                        <th>Uploaded By</th>
                        <td><?php echo htmlspecialchars($document->uploader_name ?? 'Unknown'); ?> (ID: <?php echo $document->user_id; ?>)</td>
                    </tr>
                    <tr>
                        <th>Upload Date</th>
                        <td><?php echo date('F d, Y H:i:s', strtotime($document->uploader_at)); ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if($document->status == 1): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Deleted</span>
                                <small>Deleted at: <?php echo $document->delete_at ? date('Y-m-d H:i', strtotime($document->delete_at)) : ''; ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                
                <div class="mt-3">
                    <a href="index.php?controller=document&action=securePreview&id=<?php echo $document->id; ?>" 
   class="btn btn-success" target="_blank">
    <i class="bi bi-eye"></i> Secure Preview
</a>
                    <a href="index.php?controller=document&action=edit&id=<?php echo $document->id; ?>" 
                       class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="index.php?controller=document&action=index" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Document Preview</h5>
            </div>
            <div class="card-body text-center">
                <?php if(in_array($document->file_type, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                    <img src="index.php?controller=document&action=preview&id=<?php echo $document->id; ?>" 
                         class="img-fluid" style="max-height: 150px; pointer-events: none; user-select: none;" 
                         alt="Preview" draggable="false">
                <?php elseif($document->file_type == 'pdf'): ?>
                    <i class="bi bi-file-pdf" style="font-size: 80px; color: #dc3545;"></i>
                    <p class="mt-2">PDF Document</p>
                <?php else: ?>
                    <i class="bi bi-file-lock" style="font-size: 80px; color: #6c757d;"></i>
                    <p class="mt-2"><?php echo strtoupper($document->file_type); ?> Document</p>
                <?php endif; ?>
                
                <div class="alert alert-info mt-3 mb-0 small">
                    <i class="bi bi-info-circle"></i>
                    This document is confidential. Preview only - no download option.
                </div>
                
                
            </div>
        </div>
    </div>
</div>