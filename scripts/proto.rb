PYRO_REG = /^(.*),(.*),(.*),(.*),(.*),(.*),(.*),(.*),(.*),(.*),(.*),(.*),(.*)$/

protos = Hash.new();

ARGV.each{|fileName|
   file = File.open(fileName, 'r')

   first = true

   file.each{|line|
      if (!first)
         match = line.strip.match(PYRO_REG)

         if (match)
            str = "#{match[7]}, #{match[9]}, #{match[11]}, #{match[12]}, #{match[13]}" 
            protos[str] = {:disp => match[7].gsub(" ", ""), 
             :region => match[9].gsub(" ", ""), 
             :fPrimer => match[11].gsub(" ", ""),
             :rPrimer => match[12].gsub(" ", ""), 
             :sPrimer => match[13].gsub(" ", "")}
         else
            puts "ERROR"
         end
      else
         first = false
      end
   }
}

#puts protos

id = 1

protos.each_pair{|key, val|
   dispSel = "(SELECT dispensation_id FROM dispensation_sequence WHERE dispensation_name = '#{val[:disp]}')"
   fPrimerSel = "(SELECT primer_id FROM primer WHERE sequence_name = '#{val[:fPrimer]}')"
   rPrimerSel = "(SELECT primer_id FROM primer WHERE sequence_name = '#{val[:rPrimer]}')"
   sPrimerSel = "(SELECT primer_id FROM primer WHERE sequence_name = '#{val[:sPrimer]}')"

   insert = "INSERT IGNORE INTO protocol VALUES('Protocol#{id}', #{dispSel}, #{fPrimerSel}, #{rPrimerSel}, #{sPrimerSel}, '#{val[:region]}');"

   puts insert
   id += 1
}
