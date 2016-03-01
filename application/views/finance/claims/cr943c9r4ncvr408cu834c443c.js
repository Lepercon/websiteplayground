(function ($) {
    $.cr943c9r4ncvr408cu834c443c = {
        init: function() {
            
            $('.download').click(function(){
                $.cr943c9r4ncvr408cu834c443c.download_pdf(false);
                $.cr943c9r4ncvr408cu834c443c.download_pdf(true);
            });
        },
        
        download_pdf : function(watermark){
            var doc = new jsPDF();
            var pW = doc.internal.pageSize.width;
            var pH = doc.internal.pageSize.height;
            var bdr = 20;
            var re = /<[\/]*[a-zA-Z]*>/g; 
            var fontSize = 12;

            doc.setFontSize(fontSize);
            doc.setFont('times', "normal");
            doc.setLineWidth(0.1);

            var addr = $('.jcr-address .page-content-area').html().split(re);
            addr.push($('.date').html());
            var n = bdr;

            for(i=0; i<addr.length; i++){
                if(addr[i] !== ""){
                    doc.rightText(pW-bdr, n, addr[i]);
                    n += 6;
                }
            }

            var addr = $('.bank-address .page-content-area').html().split(re);
            var n = bdr+40;

            for(i=0; i<addr.length; i++){
                if(addr[i] !== ""){
                    doc.text(bdr, n, addr[i]);
                    n += 6;
                }
            }

            doc.setLineWidth(pW-2*bdr);
            doc.text(bdr, n+=6, 'Dear Sir or Madam,');
            doc.text(bdr, n+=6, '');
            doc.text(bdr, n+=6, 'We would like to request the following bank transfer(s) from the account:');
            var text = 'Account Name: ';
            var width = doc.getStringUnitWidth(text)*fontSize/doc.internal.scaleFactor;
            doc.text(bdr, n+=6, text);
            doc.setFontStyle('bold');
            doc.text(bdr+width, n, 'Josephine Butler College JCR');
            doc.setFontStyle('normal');

            var text = 'Account Number: ';
            var width = doc.getStringUnitWidth(text)*fontSize/doc.internal.scaleFactor;
            doc.text(bdr, n+=6, text);
            doc.setFontStyle('bold');
            doc.text(bdr+width, n, '51890832');
            doc.setFontStyle('normal');

            var text = 'Sort Code: ';
            var width = doc.getStringUnitWidth(text)*fontSize/doc.internal.scaleFactor;
            doc.text(bdr, n+=6, text);
            doc.setFontStyle('bold');
            doc.text(bdr+width, n, '40-19-31');
            doc.setFontStyle('normal');

            doc.text(bdr, n+=6, '');
            doc.text(bdr, n+=6, 'Bank Transfer(s):');

            var stp = (pW-2*bdr)/5;
            var pos = -10;
            doc.setLineWidth(0.1);
            
            $('.bank-transfer-table tr td:first-of-type').parent().each(function(){
                if(pos !== n){
                    doc.setFontStyle('bold');
                    doc.centerText(bdr+0.5*stp, n+=6, 'Account Name');
                    doc.centerText(bdr+1.5*stp, n, 'Account Number');
                    doc.centerText(bdr+2.5*stp, n, 'Sort Code');
                    doc.centerText(bdr+3.5*stp, n, 'Amount');
                    doc.centerText(bdr+4.5*stp, n, 'Reference');
                    doc.setFontStyle('normal');
                    doc.line(bdr, n-4, pW-bdr, n-4);
                    doc.line(bdr, n+2, pW-bdr, n+2);
                    for(var i=0; i<=5; i++){
                        doc.line(bdr+i*stp, n-4, bdr+i*stp, n+2);
                    }
                }
                n += 6;
                var txt = "";
                var max = 0;
                for(var i = 0; i<=4; i++){
                    txt = doc.splitTextToSize($(this).children().eq(i).html(), stp-1);
                    max = Math.max(max, txt.length)
                    for(var j=0; j<txt.length; j++){
                        doc.text(bdr+i*stp+1, n+6*j, txt[j]);
                    }
                }
                n += (max-1)*6;
                
                doc.line(bdr, n+2, pW-bdr, n+2);
                for(var i=0; i<=5; i++){
                    doc.line(bdr+i*stp, n+2-6*max, bdr+i*stp, n+2);
                }
                pos = n;
                n = doc.checkRemain(n, pH, 6, bdr);
            });
            doc.text(bdr, n+=6, '');

            n = doc.checkRemain(n, pH, 6, bdr);
            var text = 'Total Number of Transfers: ';
            var width = doc.getStringUnitWidth(text)*fontSize/doc.internal.scaleFactor;
            doc.text(bdr, n+=6, text);
            doc.setFontStyle('bold');
            doc.text(bdr+width, n, $('.bank-transfer-table tr td:first-of-type').length.toFixed(0));
            doc.setFontStyle('normal');

            n = doc.checkRemain(n, pH, 30, bdr);
            doc.text(bdr, n+=6, '');
            var txt = doc.splitTextToSize('Thank you very much for your help, if you have any questions feel free to contact us in writing, or on the number you have on file for this account.\r\n\r\nYours faithfully,', doc.internal.pageSize.width-bdr*2);
            for(i=0; i<txt.length; i++){
                doc.text(bdr, n+=6, txt[i]);
            }

            n = doc.checkRemain(n, pH, 22, bdr);                
            doc.text(bdr, n+=12, 'Signed:');
            doc.text(bdr, n+=20, '');
            
            doc.line(bdr+stp*.5, n, bdr+stp*2, n);
            doc.line(bdr+stp*3, n, bdr+stp*4.5, n);

            n = doc.checkRemain(n, pH, 22, bdr);
            doc.text(bdr, n+=12, 'Printed:');
            doc.text(bdr, n+=20, '');
            doc.line(bdr+stp*.5, n, bdr+stp*2, n);
            doc.line(bdr+stp*3, n, bdr+stp*4.5, n);

            doc.text("Initial: ___ ___", 15, pH-10);
            
            var p = doc.internal.getNumberOfPages();
            for(i=1; i<=p; i++){
                doc.setPage(i);
                doc.rightText(pW-15, pH-10, "Page: "+i+" of "+p);
            }
            if(watermark){
                doc.setFontSize(30);
                for(i=1; i<=p; i++){
                    doc.setPage(i);
                    doc.centerText(pW/2, 20, 'COPY');
                    doc.centerText(pW/2, pH-20, 'COPY');
                }
                doc.output('dataurlnewwindow');
            }else{
                doc.output('dataurlnewwindow');
                doc.save('Bank Transfer ' + $('.date').attr('alt-format')+'.pdf');
            }
        }
    };
})(jQuery);


/*  End of file claims.js  */

