
function jsonToFormEncoded(data) {
    var formBody = [];
    for (var property in data) {
        var encodedKey = encodeURIComponent(property);
        var encodedValue = encodeURIComponent(data[property]);
        formBody.push(encodedKey + "=" + encodedValue);
    }
    return formBody.join("&");
}

function getPeriodMessage() {
    var period = $('input[name=options]:checked').val();
    var time = $('#time').val();

    if (period == 1)
        return time + " " + units(time, { nom: 'день', gen: 'дня', plu: 'дней' });

    if (period == 30)
        return time + " " + units(time, { nom: 'месяц', gen: 'месяца', plu: 'месяцев' });

    if (period == 365)
        return time + " " + units(time, { nom: 'год', gen: 'года', plu: 'лет' });
}

function sendMail() {
    var data = {
        phone: $('#client_phone').val(),
        name: $('#client_name').val(),
        date: $('#client_date').val(),
        message: 'Займ ' + $('#amount').val() + ' рублей на ' + getPeriodMessage() + ' с платежом: ' + $('#payment').val(),
    };

    if (data.phone == '' || data.name == '' || $('#client_phone').hasClass('invalid')) {

        if (data.phone == '') {
            $('#client_phone').addClass('invalid');
        }

        if (data.name == '') {
            $('#client_name').addClass('invalid');
        }

        return;
    }

    const config = { headers: { 'Content-Type': ' application/x-www-form-urlencoded' } };
    axios.post('send.php', jsonToFormEncoded(data), config)
        .then(function (res) {
            alert('Спасибо !');
            phone: $('#client_phone').val('');
            name: $('#client_name').val('');
            date: $('#client_date').val('');
        })
        .catch(function () {
            alert('Ошибка при отправке запроса. Попробуйте еще раз.')
        });
}

/**
* Возвращает единицу измерения с правильным окончанием
* 
* @param {Number} num      Число
* @param {Object} cases    Варианты слова {nom: 'час', gen: 'часа', plu: 'часов'}
* @return {String}            
*/
function units(num, cases) {
    num = Math.abs(num);

    var word = '';

    if (num.toString().indexOf('.') > -1) {
        word = cases.gen;
    } else {
        word = (
            num % 10 == 1 && num % 100 != 11
                ? cases.nom
                : num % 10 >= 2 && num % 10 <= 4 && (num % 100 < 10 || num % 100 >= 20)
                    ? cases.gen
                    : cases.plu
        );
    }

    return word;
}

/*
    50,000  200,000 500,000 1,000,000
14  730%    365%    183%    91%
30  365%    183%    91%     48%
90  183%    91%     48%     48%
180 91%     48%     48%     48%
365 48%     48%     48%     48%
*/

var matrix = [];
matrix.push({ order: 100, amount: 50000, time: 14, percent: 730 });
matrix.push({ order: 101, amount: 50000, time: 30, percent: 365 });
matrix.push({ order: 102, amount: 50000, time: 90, percent: 183 });
matrix.push({ order: 103, amount: 50000, time: 180, percent: 91 });
matrix.push({ order: 104, amount: 50000, time: 365, percent: 48 });
matrix.push({ order: 104, amount: 50000, time: 1100, percent: 48 });

matrix.push({ order: 105, amount: 200000, time: 14, percent: 365 });
matrix.push({ order: 106, amount: 200000, time: 30, percent: 183 });
matrix.push({ order: 107, amount: 200000, time: 90, percent: 91 });
matrix.push({ order: 108, amount: 200000, time: 180, percent: 48 });
matrix.push({ order: 109, amount: 200000, time: 365, percent: 48 });
matrix.push({ order: 119, amount: 50000, time: 1100, percent: 48 });

matrix.push({ order: 110, amount: 500000, time: 14, percent: 183 });
matrix.push({ order: 111, amount: 500000, time: 30, percent: 91 });
matrix.push({ order: 112, amount: 500000, time: 90, percent: 48 });
matrix.push({ order: 113, amount: 500000, time: 180, percent: 48 });
matrix.push({ order: 114, amount: 500000, time: 365, percent: 48 });
matrix.push({ order: 119, amount: 500000, time: 1100, percent: 48 });

matrix.push({ order: 115, amount: 1000000, time: 14, percent: 91 });
matrix.push({ order: 116, amount: 1000000, time: 30, percent: 48 });
matrix.push({ order: 117, amount: 1000000, time: 90, percent: 48 });
matrix.push({ order: 118, amount: 1000000, time: 180, percent: 48 });
matrix.push({ order: 119, amount: 1000000, time: 1100, percent: 48 });

function PMT(ir, np, pv, fv, type) {
    /*
     * ir   - interest rate per month
     * np   - number of periods (months)
     * pv   - present value
     * fv   - future value
     * type - when the payments are due:
     *        0: end of the period, e.g. end of month (default)
     *        1: beginning of period
     */
    var pmt, pvif;

    fv || (fv = 0);
    type || (type = 0);

    if (ir === 0)
        return -(pv + fv) / np;

    pvif = Math.pow(1 + ir, np);
    pmt = - ir * pv * (pvif + fv) / (pvif - 1);

    if (type === 1)
        pmt /= (1 + ir);

    return pmt;
}

function getCalculatedAmount(amount, time, period) {
    var result = matrix.filter(function (x, idx) {
        return x.amount >= amount && x.time >= (time * period);
    });

    var rate = result[0];
    var dayRate = (rate.percent / 100) / 365;
    var percentAmount = amount * dayRate * (time * period);
    var totalAmount = amount + percentAmount;
    var amountByDay = totalAmount / (time * period);
    var amountByMonth = amountByDay * 30;

    if (period == 365) {
        var annuityPMT = PMT(rate.percent / 100 / (time * 12), time * 12, amount);
    } else if (period = 30) {
        var annuityPMT = PMT(rate.percent / 100 / time, time, amount);
    }
    annuityPMT = Math.abs(annuityPMT);

    console.log('dayRate: ' + dayRate);
    console.log('percentAmount: ' + percentAmount);
    console.log('totalAmount: ' + totalAmount);
    console.log('amountByDay: ' + amountByDay);
    console.log('amountByMonth: ' + amountByMonth);
    console.log('annuityPMT: ' + annuityPMT);
    if (annuityPMT < amountByMonth) {
        amountByMonth = annuityPMT;
    }

    return {
        percentAmount: percentAmount,
        totalAmount: totalAmount,
        amountByDay: amountByDay,
        amountByMonth: amountByMonth
    };
}

new Vue({
    el: '#app',
    data: {
        amount: 150000,
        time: 2,
        period: 365,
        day: 1,
        month: 30,
        year: 365,
        min: 1,
        max: 3
    },
    computed: {
        payment: function () {
            var calculatedAmount = getCalculatedAmount(this.amount, this.time, this.period);
            if (this.time * this.period < this.month)
                return Math.round(calculatedAmount.totalAmount).toLocaleString('ru') + '₽'
            else
                return Math.round(calculatedAmount.amountByMonth).toLocaleString('ru') + '₽'
        },
        formattedAmount: function () {
            return this.amount.toLocaleString('ru') + '₽'
        },
        message: function () {
            if (this.time * this.period < this.month)
                return 'Итоговый платеж'
            else
                return 'Ежемесячный платеж'
        },
        messagePeriod: function () {
            if (this.period == this.day)
                return units(this.time, { nom: 'день', gen: 'дня', plu: 'дней' });

            if (this.period == this.month)
                return units(this.time, { nom: 'месяц', gen: 'месяца', plu: 'месяцев' });

            if (this.period == this.year)
                return units(this.time, { nom: 'год', gen: 'года', plu: 'лет' });
        }
    }
});