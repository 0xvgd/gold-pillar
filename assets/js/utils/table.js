
export const switchRowsAndCols = (thisTable, resolution) => {
    if ($(window).width() < resolution) {
        switchRowsAndColsInnerFun(thisTable);
    }
    $(window).resize(function () {
        if ($(window).width() < resolution) {
            switchRowsAndColsInnerFun(thisTable);
        }else{
            switchRowsAndColsRemove(thisTable);
        }
    });
};

export const switchRowsAndColsRemove = (thisTable) => {
    $("tr > *", thisTable).css({
        height: 'auto'
    });
};

export const switchRowsAndColsInnerFun = (thisTable) => {
    var maxRow = $("tr:first-child() > *", thisTable).length;

    for (var i = maxRow; i >= 0; i--) {

        $("tr > *:nth-child(" + i + ")", thisTable).css({
            height: 'auto'
        });

        var maxHeight = 0;

        $("tr > *:nth-child(" + i + ")", thisTable).each(function () {
            var h = $(this).height();
            maxHeight = h > maxHeight ? h : maxHeight;
        });

        $("tr > *:nth-child(" + i + ")", thisTable).each(function () {
            $(this).height(maxHeight);
        });
    };
};