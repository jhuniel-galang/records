<div class="mb-4">
    <h2>Form 137 Details</h2>
    <div class="mt-2">
        <a href="index.php?controller=form137&action=printForm&id=<?php echo $record['id']; ?>" class="btn btn-warning" target="_blank">
            <i class="bi bi-printer"></i> Print Form 137
        </a>
        <a href="index.php?controller=form137&action=index" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Student Information (Extracted from PDF)</h5>
            </div>
            <div class="card-body">
                <?php $extracted = $record['extracted_data'] ?? []; ?>
                
                <?php if(!empty($extracted)): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i> Student data was successfully extracted from the PDF.
                </div>
                
                <table class="table table-bordered">
                    <?php if(isset($extracted['lrn'])): ?>
                    <tr>
                        <th style="width: 200px;">LRN</th>
                        <td><?php echo htmlspecialchars($extracted['lrn']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(isset($extracted['student_name'])): ?>
                    <tr>
                        <th>Student Name</th>
                        <td><strong><?php echo htmlspecialchars($extracted['student_name']); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(isset($extracted['birth_date'])): ?>
                    <tr>
                        <th>Birth Date</th>
                        <td><?php echo htmlspecialchars($extracted['birth_date']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(isset($extracted['gender'])): ?>
                    <tr>
                        <th>Gender</th>
                        <td><?php echo htmlspecialchars($extracted['gender']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(isset($extracted['school'])): ?>
                    <tr>
                        <th>School</th>
                        <td><?php echo htmlspecialchars($extracted['school']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(isset($extracted['grade_level'])): ?>
                    <tr>
                        <th>Grade Level</th>
                        <td><?php echo htmlspecialchars($extracted['grade_level']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(isset($extracted['school_year'])): ?>
                    <tr>
                        <th>School Year</th>
                        <td><?php echo htmlspecialchars($extracted['school_year']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(isset($extracted['general_average'])): ?>
                    <tr>
                        <th>General Average</th>
                        <td><strong><?php echo htmlspecialchars($extracted['general_average']); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> No student data could be extracted from this PDF. 
                    Please ensure the PDF contains text content.
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Document Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">File Name</th>
                        <td><?php echo htmlspecialchars($record['file_name']); ?></td>
                    </tr>
                    <tr>
                        <th>School</th>
                        <td><?php echo htmlspecialchars($record['school_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Document Type</th>
                        <td><span class="badge bg-danger"><?php echo htmlspecialchars($record['document_type']); ?></span></td>
                    </tr>
                    <tr>
                        <th>File Size</th>
                        <td><?php echo round($record['file_size'] / 1024, 2); ?> KB</td>
                    </tr>
                    <tr>
                        <th>Uploaded By</th>
                        <td><?php echo htmlspecialchars($record['uploader_name'] ?? 'Unknown'); ?></td>
                    </tr>
                    <tr>
                        <th>Upload Date</th>
                        <td><?php echo date('F d, Y h:i A', strtotime($record['uploader_at'])); ?></td>
                    </tr>
                    <?php if($record['remarks']): ?>
                    <tr>
                        <th>Remarks</th>
                        <td><?php echo nl2br(htmlspecialchars($record['remarks'])); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Preview</h5>
            </div>
            <div class="card-body text-center">
                <i class="bi bi-file-pdf" style="font-size: 80px; color: #dc3545;"></i>
                <p class="mt-2">Form 137 Document</p>
                <a href="index.php?controller=document&action=preview&id=<?php echo $record['id']; ?>" 
                   class="btn btn-success btn-sm" target="_blank">
                    <i class="bi bi-eye"></i> View PDF
                </a>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Generate Certificates</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="index.php?controller=form137&action=certificate&id=<?php echo $record['id']; ?>&type=good_moral" 
                       class="btn btn-success" target="_blank">
                        <i class="bi bi-award"></i> Good Moral Certificate
                    </a>
                    <a href="index.php?controller=form137&action=certificate&id=<?php echo $record['id']; ?>&type=enrollment" 
                       class="btn btn-info" target="_blank">
                        <i class="bi bi-file-text"></i> Enrollment Certificate
                    </a>
                    <a href="index.php?controller=form137&action=certificate&id=<?php echo $record['id']; ?>&type=completion" 
                       class="btn btn-primary" target="_blank">
                        <i class="bi bi-trophy"></i> Completion Certificate
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>