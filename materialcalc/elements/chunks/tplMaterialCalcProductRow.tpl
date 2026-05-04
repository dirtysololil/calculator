<li class="materialcalc-list__item">
    <div class="materialcalc-list__name">
        [[+name]]

        
    </div>

    <div class="materialcalc-list__meta">
        <span>Расход: [[+amount_formatted]] шт</span>

        [[+weight:notempty=`
            <span>Вес: [[+weight]] [[+weight_unit_label]]</span>
        `]]

        [[+width:notempty=`
            <span>Ширина: [[+width]] [[+size_unit_label]]</span>
        `]]

        [[+length:notempty=`
            <span>Длина: [[+length]] [[+size_unit_label]]</span>
        `]]

        [[+height:notempty=`
            <span>Высота: [[+height]] [[+size_unit_label]]</span>
        `]]

        [[+depth:notempty=`
            <span>Глубина: [[+depth]] [[+size_unit_label]]</span>
        `]]
        [[+color:notempty=`
            <span class="materialcalc-list__color">Цвет: [[+color]]</span>
        `]]
    </div>

    <div class="materialcalc-list__sum">
        [[+sum_formatted]] ₽
    </div>
</li>